<?php
    // PHP CORS Access
    /*switch ($_SERVER['HTTP_ORIGIN']) {
        case 'http://example.loc':
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        break;
    } */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Store from Ecwid API</title>
        <link rel="stylesheet" href="/styles/style.css">
    </head>
    <body>
        <div class="product-count">
            <div class="product-count__block">
                <div class="product-count__title">
                    Test Store from Ecwid API
                </div>
                Количество продуктов в магазине: <span id="count-products"></span>
            </div>
            <div class="product-count__block" id="putInOrder">
                <div class="product-count__text">
                    Количество товаров в корзине: <span id="count-products-cart">0</span>
                </div>
                <button class="product-count__button" onclick="addInCartAPI(localCart)">Сформировать заказ</button>
            </div>
        </div>
        
        <div class="product-list" id="product-list"></div>
        
        <script type="text/javascript" src="https://app.ecwid.com/script.js?{storeId}&data_platform=code&data_date=2021-02-15" charset="utf-8"></script>
        <script>
            let remoteCart = <?php echo $_POST['items'] ?? []; ?>,       // Set Data From Remote Cart
                localCart = [],                                          // Set Local Cart For Added Product in Page
                storeId = 0,                                             // Set Ecwid StoreID
                publicToken = 'public_token',                            // Set Public Ecwid API Access Token
                cartCount = 0;                                           // Set Total Counter Product Cart

            // Add Products From Remote Cart
            if (remoteCart.length > 0) {            
                Ecwid.OnAPILoaded.add(function () {
                    addInCartAPI(remoteCart);
                });
            }

            function cartQuantity() {
                Ecwid.OnAPILoaded.add(function () {
                    Ecwid.Cart.get(function(cart) {
                        if (cart.productsQuantity > 0) {
                            cartCount = cart.productsQuantity
                            document.querySelector('#putInOrder').style.display = 'flex';
                            document.querySelector('#count-products-cart').innerHTML = cart.productsQuantity;
                        } else {
                            document.querySelector('#putInOrder').style.display = null;
                        }
                    });
                });
            }

            // Add Product in Cart (Custom)
            function addInCart(id, quantity = 1) {
                localCart.push({
                    id: id,
                    quantity: quantity
                });

                if (localCart.length > 0) {
                    document.querySelector('#putInOrder').style.display = 'flex';
                    document.querySelector('#count-products-cart').innerHTML = localCart.length + cartCount;
                } else {
                    document.querySelector('#putInOrder').style.display = null;
                }
            }

            // Event: Add Product in Cart
            function addInCartAPI(localCart) {
                if (localCart.length > 0) {
                    localCart.forEach(element => {
                        Ecwid.Cart.addProduct({
                            id: element.id,
                            quantity: element.quantity, 
                            callback: function (success, product, cart){
                                if (success) {
                                    Ecwid.openPage('cart');
                                }
                            }
                        });
                    });

                    localCart.splice(0, localCart.length);
                } else {
                    Ecwid.openPage('cart');
                }
            }

            Ecwid.OnAPILoaded.add(function () {
                Ecwid.OnCartChanged.add(function(cart) {
                    cartQuantity();
                });
            });

            // Open Cart Before Adding Product in Cart
            Ecwid.OnAPILoaded.add(function () {
                addInCart(id, quantity = 1);
                Ecwid.openPage('cart');
            });

            // Get Product List from Ecwid API
            fetch('https://app.ecwid.com/api/v3/' + storeId + '/products?token=' + publicToken, {
                method: 'GET'
            })
            .then((response) => {
                return response.json();
            }).then((data) => {
                document.querySelector('#count-products').innerHTML = data.count;
                data.items.forEach(element => {
                    document.querySelector('#product-list').innerHTML += `
                    <div class="product-item">
                        <div class="product-item__image">
                            <img src="${element.thumbnailUrl}">
                        </div>
                        <div class="product-item__title">
                            ${element.name}
                        </div>
                        <div class="product-item__price">
                            ${element.defaultDisplayedPriceFormatted}
                        </div>
                        <div class="product-item__desc">
                            ${element.description}
                        </div>
                        <button class="product-item__button" onclick="addInCart(${element.id}, 1)">Добавить в корзину</button>
                    </div>
                    `;
                });
            });

            // Post Data Function
            // For POST Request - required secret access token
            /*
                async function postData(url = '', data = {}, methods = 'POST') {
                    const response = await fetch(url, {
                        method: methods,
                        mode: 'cors',
                        cache: 'no-cache',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        redirect: 'follow',
                        referrerPolicy: 'no-referrer',
                        body: JSON.stringify(data)
                    });
                    return await response.json();
                }
            */
            
            // Add New Product in Ecwid API
            /*
                postData('https://app.ecwid.com/api/v3/' + storeId + '/products?token=' + secretToken, {
                    name: "Test Product from API"
                }).then((data) => {
                    console.log(data);
                });
            */
        </script>
    </body>
</html>