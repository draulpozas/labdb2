var rgts = [];

function getRgts() {
    let inputField = document.getElementById('query');
    let query = inputField.value;
    inputField.value = '';

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.status == 200 & this.readyState == 4) {
            // console.log(JSON.parse(this.responseText));
            return JSON.parse(this.responseText);
        }
    }
    xhttp.open('GET', '/reagent/finder?name='+query, false);
    xhttp.send();
}

function find() {
    rgts = getRgts();
    // console.log(getRgts());
    let table = document.getElementById('resultsTable');
    if (typeof rgts == "undefined" || rgts.length == 0) {
        table.innerHTML = `<tr><td colspan="5">no records found</td></tr>`;
    } else {
        rgts.forEach(rgt => {
            let tr = document.createElement('tr');

            appendTd(rgt.name, tr);
            appendTd(rgt.formula, tr);
            appendTd(rgt.cas, tr);
            appendTd(rgt.notes, tr);
            appendTd(`<a href="${rgt.id}">show</a><a href="${rgt.id}/edit">edit</a>`, tr);
            table.appendChild(tr);
        });
    }
}

function appendTd(inner, parentTr) {
    let td = document.createElement('td');
    td.innerHTML = inner;
    parentTr.appendChild(td);
}