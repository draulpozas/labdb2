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
    loading.style.display = 'inline-block';
    getRgts();
    let table = document.getElementById('resultsTable');
    // if (rgts.length == 0) {
    if (typeof rgts == "undefined" || rgts.length == 0) {
        table.innerHTML = `<tr><td colspan="5">no records found</td></tr>`;
    } else {
        table.innerHTML = '';
        rgts.forEach(rgt => {
            let tr = document.createElement('tr');

            appendTd(rgt.name, tr, 'reagentname');
            appendTd(rgt.formula, tr);
            appendTd(rgt.cas, tr);
            appendTd(rgt.notes, tr);
            appendTd(`<a href="${rgt.id}">🗎</a> | <a href="${rgt.id}/edit">🖉</a>`, tr);
            table.appendChild(tr);
        });
    }
    loading.style.display = 'none';
}

function getRgts() {
    let inputField = document.getElementById('query');
    let query = inputField.value;
    inputField.value = '';

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.status == 200 & this.readyState == 4) {
            rgts = JSON.parse(this.responseText);
        }
    }
    xhttp.open('GET', '/reagent/finder?name='+query, false);
    xhttp.send();
}

function appendTd(inner, parentTr, addClass = null) {
    let td = document.createElement('td');
    td.innerHTML = inner;
    if (addClass != null) {
        td.classList.add(addClass);
    }
    parentTr.appendChild(td);
}