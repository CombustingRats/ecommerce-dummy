// ASSIGNMENT 3

if (document.readyState == 'loading') {
    /* 
    checks to see if the page is still loading before running any js code
    */

    document.addEventListener('DOMContentLoaded', ready)
} else {
    ready()
}


function ajax (opt) {
    /* 
    The ajax function from the lecture
    */

    opt = opt || {};
    var xhr = (window.XMLHttpRequest)                  // Usu. ?/|| is for compatibility
            ? new XMLHttpRequest()                     // IE7+, Firefox1+, Chrome1+, etc
            : new ActiveXObject("Microsoft.XMLHTTP"),  // IE 6
        async = opt.async || true,
        success = opt.success || null, error = opt.error || function(){/*displayErr()*/};
    // pass three parameters, otherwise the default ones, to xhr.open()
    xhr.open(opt.method || 'GET', opt.url || '', async); // 3rd param true = async
    if (opt.method == 'POST') 
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // Asyhronous Call requires a callback function listening on readystatechange
    if (async)
      xhr.onreadystatechange = function(){
        if (xhr.readyState == 4) { // 4 is "more ready" than 3 or 2	
		  var status = xhr.status;
          if ((status >= 200 && status < 300) || status == 304 || status == 1223)
            success && success.call(xhr, xhr.responseText); // raw content of the response
          else if (status < 200 || status >= 400)
            error.call(xhr);
        }
      };
    xhr.onerror = function(){error.call(xhr)};
    // POST parameters encoded as opt.data is passed here to xhr.send()
    xhr.send(opt.data || null);
    // Synchronous Call blocks UI and returns result immediately after xhr.send()
    !async && success && success.call(xhr, xhr.responseText);
};



function ready() {

    /* 
    runs if the page is fully loaded, we add the cart buttons 
    */

    populateCart() //Populate the cart dropdown list from local storage

    // Add event listeners to each of the 'add to cart' buttons
    var addToCartButtons = document.getElementsByClassName('add-cart')
    for (var i = 0; i < addToCartButtons.length; i++) {
        var button = addToCartButtons[i]
        button.addEventListener('click', addToCartClick)
    }   
}

function populateCart() {

    /*
    Populates the cart display from the local storage. Calls updateCartTotal to update the total price
    */

    // First clear the cart items and add back just the checkout button and cart total 
    var cartItems = document.getElementsByClassName('cart-items')[0];
    if (cartItems == null) return;
    cartItems.innerHTML = '<li class="cart-total"></li><li class="text-center"><button type="submit" href="cart.html" class="btn">Checkout</button></li>';

    // retrieve the localstorage JSON and then generate a cartrow for each cartitem, add the row to the cart display
    localItems = JSON.parse(localStorage.getItem("items"))
    if (localItems) {
        // iterate over the products in localstorage and add them to the cart dropdown
        Object.values(localItems).map(item => { 

            cartRow = document.createElement('li')
            cartRow.classList.add('cart-row')
            cartRow.classList.add('grid')
            
            var cartRowContents =  `
            <p class = "cart-item-name">${item.name}</p>
            <p class="item-price">${item.price}</p>
            <p>Qty:</p>
            <input class="item-quantity" name="quantity[${item.id}]" data-product-id="${item.id}" type="number" value="${item.quantity}" min="1" max="99">              
            <button class="btn btn-remove" data-product-id="${item.id}">Remove</button>
            `
            
            cartRow.innerHTML = cartRowContents
            cartItems.prepend(cartRow)

            cartRow.getElementsByClassName('btn-remove')[0].addEventListener('click', removeCartItem)
            cartRow.getElementsByClassName('item-quantity')[0].addEventListener('change', quantityChanged) 

        })
        updateCartTotal()   
    }
}

function removeCartItem(event) {
    /*
    removes the clicked item from localstorage before repopulating the cart
    */

    var buttonClicked = event.target

    // get the item id of the item removed
    var id = buttonClicked.dataset.productId;
    //buttonClicked.parentElement.remove()

    var items = JSON.parse(localStorage.getItem('items'));
    for (var i=0; i < items.length; i++) {
        var item = items[i];

        if (item.id == id) {
            // removes one element at index i
            items.splice(i, 1);
            break;
        }
    }
    localStorage.setItem('items', JSON.stringify(items))

    populateCart();
    //updateCartTotal()   
}


function quantityChanged(event) {
    /*
    updates the quantity of the item in localstorage and resets the cart total display
    */

    // Don't let the input value be less than 0 or none
    var input = event.target
    if (isNaN(input.value) || input.value <= 0) {
        input.value = 1
    }

    // If the id in localstorage matches that of the item, change the quantity value to the new input

    var id = input.dataset.productId;
    var items = JSON.parse(localStorage.getItem('items'));
    for (var i=0; i < items.length; i++) {
        var item = items[i];

        if (item.id == id) {
            items[i].quantity = parseInt(input.value)
            break;
        }
    }
    localStorage.setItem('items', JSON.stringify(items))

    updateCartTotal()
}

function addToCartClick(event) {
    /*
    Code that runs after the client presses the button to add an item to the cart
    */

    var button = event.target
    // In case the user has clicked on the icon rather than the button
    if (button.tagName == "I") {
        button = button.parentElement
    }
    // console.log(button);
    // var shopItem = button.parentElement

    // get the pid, which will be present in the button tag's database (code this during product display generation)
    var itemId = button.dataset.productId

    ajax({
        /*
        (Step 2.2 in assignment)
        uses ajax to get the name & price we want and passing them to addItemToCart()
        */
        url:'product_info.php?id='+itemId,
        success: function(m){
            var result = JSON.parse(m)
            var itemName = result.name 
            var itemPrice = result.price 
            addItemToCart(itemId, itemName, itemPrice)
            //updateCartTotal()
        }
    });
}

function addItemToCart(itemId, itemName, itemPrice) {
    /*
    Adds the new item's pid, name and price details to localstorage, then calls populateCart() to add the new items to the display
    */

    var items = JSON.parse(localStorage.getItem('items'));
    if (items) {
        for (var i=0; i < items.length; i++) {
            // Iterate over all the items in localstorage and see if theres a clash in itemids. if so, its already in the cart
            var item = items[i];

            if (item.id == itemId) {
                alert('This item is already in the cart!!!')
                return;
            }
        }
    }

    //add the new product to local storage. 

    var items = JSON.parse(localStorage.getItem('items')) || [] // If its the first item, make items an empty array
    var item = {
        id: itemId,
        name: itemName,
        price: itemPrice,
        quantity: 1
    }

    items.push(item)
    localStorage.setItem('items', JSON.stringify(items))

    // Repopulate the cart with the new item
    populateCart();
}

function updateCartTotal() {
    /*
    sum up the products of the item prices and quantities and display it in the cart totals tag
    */
    var total = 0;
    var items = JSON.parse(localStorage.getItem('items'));
    if (items) {
        for (var i=0; i < items.length; i++) {
            var item = items[i]
            total += (item.price * item.quantity)
        }
    }

    document.getElementsByClassName('cart-total')[0].innerText = 'Cart Total: $' + total

}

// ASSIGNMENT FIVE

function checkout() {
    var form = document.getElementById('cart');
    var inputs = form.getElementsByTagName('input');
}

function beforeCheckout() {
    // Retrieve the data from the checkout form
    var form = document.getElementsByTagName('form')[0];
    var formData = new FormData(form);
    var dataStr = '';

    /*for (var pair of formData.entries()) {
        console.log(pair[0]+ ', ' + pair[1]); 
    };*/

    var currentItem = 1;

    while (formData.has('item_id_' + currentItem)) {
        var key = 'item_id_' + currentItem;
        var key2 = 'quantity_' + currentItem;

        if (currentItem > 1) dataStr += '&';
        // Return the values for itemid and quantity from the form and store them in array
        dataStr += 'item_id[]=' + formData.get(key) + '&' + 'quantity[]=' + formData.get(key2);
        currentItem++;
    }

    console.log(dataStr);

    ajax({
        /*
        Uses AJAX to enter the order information into the database, before submitting to paypal
        */
        url:'saveorder.php',
        method:'POST',
        data: dataStr,
        success: function(m){
            console.log(m);
            m = JSON.parse(m);
            // retrieve the lastInsertId and the hash from the server and add it to the appropriate fields, 
            //before submitting to paypal check what comes out here
            var invoice = m['lastInsertId'];
            var custom = m['hashValue'];

            document.getElementById('invoice').value = invoice;
            document.getElementById('custom').value = custom;

            form.submit();

            // Clear the local storage
            // localStorage.removeItem('items');           
        }
    });

    // Cancel the original submission mechanism
    return false;
}