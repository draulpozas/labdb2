var rgts = [];

// function triggerFind() {
//     setTimeout(find, 250);
// }

function ifenter(ev) {
    if (ev.key == 'Enter') {
        find();
    }
}

function find() {
    let loading = document.getElementById('loading');
    loading.style.opacity = '1';
    getRgts();
    let table = document.getElementById('resultsTable');
    // if (rgts.length == 0) {
    if (typeof rgts == "undefined" || rgts.length == 0) {
        table.innerHTML = `<tr><td colspan="5">no records found</td></tr>`;
    } else {
        table.innerHTML = '';
        rgts.forEach(rgt => {
            let tr = document.createElement('tr');

            appendTd(rgt.name, tr);
            appendTd(rgt.formula, tr);
            appendTd(rgt.cas, tr);
            appendTd(rgt.notes, tr);
            appendTd(`<a href="${rgt.id}">show</a> | <a href="${rgt.id}/edit">edit</a>`, tr);
            table.appendChild(tr);
        });
    }
    loading.style.opacity = '0';
}

function getRgts() {
    let inputField = document.getElementById('query');
    let query = inputField.value;
    // inputField.value = '';

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.status == 200 & this.readyState == 4) {
            rgts = JSON.parse(this.responseText);
        }
    }
    xhttp.open('GET', '/reagent/finder?name='+query, false);
    xhttp.send();
}

function appendTd(inner, parentTr) {
    let td = document.createElement('td');
    td.innerHTML = inner;
    parentTr.appendChild(td);
}