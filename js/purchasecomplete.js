// Clear the local storage

if (document.readyState == 'loading') {
    /* 
    checks to see if the page is still loading before running any js code
    */

    document.addEventListener('DOMContentLoaded', ready)
} else {
    ready()
}

function ready() {
    localStorage.removeItem('items'); 
}
