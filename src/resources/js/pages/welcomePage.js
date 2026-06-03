function getElement(id) {
    return document.getElementById(id);
}

function toggleWidget() {
    const toggleButton = getElement('toggleWidgetBtn');
    const widgetContainer = getElement('widgetContainer');

    const isHidden = widgetContainer.classList.contains('d-none');
    widgetContainer.classList.toggle('d-none');
    toggleButton.textContent = isHidden ? 'Hide ticket widget' : 'Open ticket widget';
}

document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = getElement('toggleWidgetBtn');
    if (!toggleButton) {
        return;
    }

    toggleButton.addEventListener('click', toggleWidget);
});
