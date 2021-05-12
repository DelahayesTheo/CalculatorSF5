//Buttons logic
const screen = document.getElementById("calculator-screen");
const keys = document.getElementsByClassName("calculator-key");
const operatorRegex = /[\*\/\-\+]{1}/;


for(key of keys) {
    if(key.classList.contains("operator")) {
        let operatorAction = key.dataset.action;
        switch (operatorAction) {
            case "divide":
            case "add":
            case "substract":
            case "multiply":
                key.addEventListener('click', e => {
                    const keyContent = e.target.textContent;
                    if(checkForDoubleOperator(keyContent)) {
                        inputToScreen(keyContent)
                    }
                });
                break;
            case "decimal":
                key.addEventListener('click', e => {
                    addDecimalToDisplay();
                });
                break;
            case "equal":
                key.addEventListener('click', e => {
                    fetchData();
                });
                break;
            case "clear":
                key.addEventListener('click', e => {
                    screen.value = "0";
                });
                break;
        }
    } else {
        key.addEventListener('click', e => {
            const keyContent = e.target.textContent;
            inputToScreen(keyContent);
        });
    }
}



//Numpad logic
const charCodesWhitelist = [
    48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,106,107, //0-9
    106,107,109,110,111,187,190,191,220, //Operators
    13, 8 //Enter and backspace
];

screen.addEventListener("keydown", e => {
    e.preventDefault();
    //For the numpad stuff we need to improvise a little
    let keyPressed = e.keyCode;
    let screenCurrentDisplay = screen.value;
    if(charCodesWhitelist.includes(keyPressed)) {
        switch (keyPressed) {
            case 13:
                fetchData();
                break;
            case 8:
                if(screenCurrentDisplay.length > 1) {
                    screen.value = screen.value.substring(0, screen.value.length - 1);
                } else {
                    screen.value = 0;
                }
                break;
            default:
                //Some keycode have multiple keys attached, we need to make sure it's one of the good ones
                if(e.key.match(/[0-9\*\-\+\.\/]{1}/)) {
                    //We go regex or strong comparaison, switches are too hectic for the multiple keycodes
                    if(e.key === ".") {
                        addDecimalToDisplay();
                    } else if (
                        (e.key.match(operatorRegex) !== null && checkForDoubleOperator(e.key))
                        ||
                        (e.key.match(/[0-9]{1}/) !== null)
                        ) {
                        inputToScreen(e.key)
                    }
                }
                break;
        }
    }
})


//Functions and stuff

/*
    Check if we inputting the operator to the string is allowed
*/
const checkForDoubleOperator = input => {
    let screenCurrentDisplay = screen.value;
    let lastCharOfScreenCurrentDisplay = screenCurrentDisplay.charAt(screenCurrentDisplay.length - 1);
    if (lastCharOfScreenCurrentDisplay.match(operatorRegex) === null && lastCharOfScreenCurrentDisplay !== ".") {
        return true;
    } else if (screenCurrentDisplay.length >= 2) {
        // Case for stuff like - -3
        let secondLastCharOfScreenCurrentDisplay = screenCurrentDisplay.charAt(screenCurrentDisplay.length - 2);
        if (input === "-"
         && secondLastCharOfScreenCurrentDisplay.match(operatorRegex) === null
         && lastCharOfScreenCurrentDisplay.match(operatorRegex) !== null) {
            return true;
        }
    }
    return false;
}

/*
    Input a given char to screen
*/
const inputToScreen = input => {
    let screenCurrentDisplay = screen.value;
    if(screenCurrentDisplay === '0' && input.match(operatorRegex) === null) {
        screen.value = input;
    } else {
        screen.value = screenCurrentDisplay + input;
    }
}



const fetchData = () => {
    let data = new FormData();
    let valueUsed = screen.value;
    data.append("json", JSON.stringify(valueUsed));
    let opts = {
        method: 'POST',
        body: data
    };
    fetch("/calculate", opts)
        .then(res => {return res.json()})
        .then(data => {
            if (data.status === "success") {
                let resultArea = document.getElementById("result");
                resultArea.classList.add("alert-success");
                resultArea.innerHTML = valueUsed + " = " + data.result;
                screen.value = "0";
            } else {
                let dumpArea = document.getElementById("dump");
                dumpArea.classList.add("alert-danger");
                dumpArea.innerHTML = data.result;
            }
        });
}

const addDecimalToDisplay = () => {
    let screenCurrentDisplay = screen.value;
    let operatorChars = "*-+/";
    let stringOfLastNumber = screenCurrentDisplay;
    let posOfOperator = 0;
    for(operator of operatorChars) {
        posOfOperator = (stringOfLastNumber.lastIndexOf(operator) === -1 ? 0 : stringOfLastNumber.lastIndexOf(operator));
        stringOfLastNumber = posOfOperator === 0 ? stringOfLastNumber : stringOfLastNumber.substr(posOfOperator + 1); 
    }

    //No decimal added to last number we can go for it, we need the regex has js would automaticaly convert . into any char
    if (stringOfLastNumber === "") {
        screen.value += "0";
    }
    if(stringOfLastNumber.search(/\./) === -1) {
        screen.value += ".";
    }
}
