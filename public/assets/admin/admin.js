/**
 * Remove an element from the DOM.
 * 
 * @param {String} elementId    element to remove id 
 */
function deleteAction(elementId) {
    const element = document.querySelector(`#${elementId}`);
    if (element) {
        element.remove();
    }
}

/**
 * Create a alert that disappear after 5 seconds. 
 * @param {String} type     alert type, success or error
 * @param {String} text     the message to display
 */
function toastify(type = 'success', text = null) {
    // create container if not exist
    let container = document.querySelector('.toastify-container');
    if (!container) {
        container = document.createElement('div');
        container.classList = 'toastify-container';
        document.body.appendChild(container);

    }

    const toastifyId = "toastify-" + Date.now();
    const messages = {
        success: "Les données ont bien été sauvegardée",
        error: "L'opération a échoué"
    };
    const validStatus = ['success', 'error'];
    type = (validStatus.includes(type)) ? type : 'success';

    const div = document.createElement('div');
    if (type === 'success') {
        div.classList = 'toastify toastify-success';
    } else {
        div.classList = 'toastify toastify-error';
    }
    div.id = toastifyId;
    const buttonClass = (type === 'success') ? 'btn-success' : 'btn-danger';
    let message = (text instanceof String) ? text : messages[type]
    div.innerHTML = `<div>${message}</div> <button type="button" class="btn btn-sm ${buttonClass}" onclick="deleteAction('${toastifyId}')"><i class="fa fa-times"></i></button>`;
    container.appendChild(div);

    setTimeout(function () {
        deleteAction(toastifyId);
    }, 3000);
}

/**
 * Create a sortable list.
 * 
 * @param {String} list     selector of the list
 * @param {String} path     the path used to send the updated data
 */
async function sortable(list, path) {
    const events = document.querySelector(list);
    if (!events) {
        return;
    }
    await Sortable.create(events, {
        dataIdAttr: 'data-order',
        ghostClass: 'blue-background-class',
        onEnd: function (evt) {
            const data = [];

            for (let i = 0; i < events.children.length; i++) {
                const element = events.children[i];
                data.push({
                    id: element.children[0].innerText,
                    position: i
                })

                element.children[2].innerText = i;
            }

            fetch(path, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error("Il y a eu une erreur et les données n'ont pas été sauvegardées.");
                    }
                })
                .then(response => toastify('success', JSON.stringify(response)))
                .catch(error => toastify('error', error));
        }
    });
}

/**
 * Change the type of a password input to hide or show the password.
 * 
 * @param {String} targetId 
 */
function togglePassword(targetId) {
    const input = document.querySelector(`#${targetId}`);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

(function () {
    'use strict'
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })

    const menus = document.querySelectorAll('.btn-menu');
    menus.forEach(menu => {

        menu.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const parent = menu.parentElement;
            if (parent) {
                parent.classList.toggle('menu-open');
            }
        })
    });
})()

const elements = document.querySelectorAll('[data-choices]');
elements.forEach(element => {
    new Choices(element, {
        removeItems: true,
        removeItemButton: true,
        allowHTML: false,
        noResultsText: 'Aucun résultat',
        noChoicesText: 'Aucun élément à choisir',
        itemSelectText: 'Cliquez pour choisir',
        shouldSort: false,
    });
});