function addCollectionAttribute() {
    const collectionHolder = document.querySelector('#custom-attributes-wrapper');
    const item = document.createElement('div');
    item.className = 'item';
    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );
    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;
    addRemoveAttributeButton(item);
}

function addRemoveAttributeButton(item) {
    const removeFormButton = document.createElement('a');
    removeFormButton.href = '#';
    removeFormButton.innerText = 'Delete attribute';
    item.append(removeFormButton);
    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        item.remove();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document
        .querySelectorAll('.add-custom-attribute')
        .forEach(el => {
            el.addEventListener("click", (e) => {
                e.preventDefault();
                addCollectionAttribute();
            })
        });
    document
        .querySelectorAll('#custom-attributes-wrapper div.item')
        .forEach(el => {
            el.addEventListener("click", (e) => {
                e.preventDefault();
                addRemoveAttributeButton(el);
            })
        });
});