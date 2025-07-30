import { createRootRoute, Link, Outlet } from '@tanstack/react-router'
import { TanStackRouterDevtools } from '@tanstack/react-router-devtools'
import { useCart, type Item } from 'react-use-cart'
import CartIcon from '../icons/CartIcon'
import CartEmpty from '../icons/CartEmpty'
import BagIcon from '../icons/Bag'
import { useState, useEffect, useRef } from 'react'
import CartAttributeSelector from '../components/CartAttributeSelector'
import logger from '../components/logger'
import {startCase, camelCase} from 'lodash';

async function PlaceOrderMutation(items: Item[]) {
    const modifiedItems = items.map(({ allAttributes, ...rest }) => rest);
    // logger.info('Items to be placed in the order');
    // logger.info(JSON.stringify(modifiedItems));
    const response = await fetch(import.meta.env.VITE_API_URL, {
        method: 'POST',
        body: JSON.stringify({
            query: `
                mutation createOrder($items: OrderInput!){
                    createOrder(items: $items) {
                        id
                    }
                }
            `,
            variables: {
                items: {
                    items: JSON.stringify(modifiedItems)
                }
            }
        })
    })
    // logger.info(response.json())
    return response.json()
}

async function PlaceOrder(items: Item[]) {
    let res = await PlaceOrderMutation(items);
    if (res.errors) {
        alert("Error placing order");
        console.error("Error:", res.errors[0].message)
        return;
    }
    alert("Order placed");
}

async function getCategories(){
    const response = await fetch(import.meta.env.VITE_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: `
        {
            getCategories {
                name
            }
        }
            `,
        })
    })
    return response.json()
}

interface CartProps {
    initialOpen?: boolean; // New prop to control initial open state
}

function useCategories() {
    const [categories, setCategories] = useState<string[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        async function fetchCategories() {
            try {
                // Added try...catch for better error handling
                const res = await getCategories();
                // Check if res.data exists and then res.data.getCategories
                // Also good to check if res.data.getCategories is an array
                if (res && res.data && Array.isArray(res.data.getCategories)) {
                    setCategories(res.data.getCategories.map((cat: { name: string }) => cat.name));
                } else {
                    console.warn("Unexpected API response structure:", res);
                }
            } catch (error) {
                console.error("Error fetching categories:", error);
            } finally {
                setLoading(false); // Ensure loading is set to false
            }
        }
        fetchCategories();
    }, []);
    
    return { categories, loading };
}

export function getCartItemId(productId: string, attributes: Record<string, string>) {
    // Create a string that uniquely identifies the combination
    return productId + '-' + Object.entries(attributes).sort().map(([k, v]) => `${k}:${v}`).join('|');
}

function Cart({ initialOpen = false }: CartProps) {
    const { isEmpty, totalUniqueItems, items, updateItemQuantity, removeItem, emptyCart } = useCart();

    const [isOpen, setIsOpen] = useState(initialOpen);
    const isFirstRender = useRef(true); // Track first render

    // Effect to open cart when totalUniqueItems changes (i.e., an item is added)
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return; // Skip effect on first render
        }
        if (!isEmpty && !isOpen) {
            setIsOpen(true);
            logger.info('Cart opened')
            logger.info(items)
        }
    }, [totalUniqueItems]); // Re-run when totalUniqueItems changes

    useEffect(() => {
        if (isOpen && items.length === 0) {
            setIsOpen(false);
        }
    }, [items, isOpen]);

    // Calculate total price
    const totalPrice = items.reduce(
        (acc, item) => acc + (item.price ?? 0) * (item.quantity ?? 0),
        0,
    );

    if (isEmpty && !isOpen)
        return (
            <div data-testid='cart-btn' className='flex row' onClick={() => setIsOpen(true)}>
                <CartEmpty />
            </div>
        );

    if (!isEmpty && !isOpen)
        return (
            <div className="flex row justify-between" onClick={() => setIsOpen(true)} data-testid='cart-btn'>
                <div className="relative flex items-center">
                    <CartIcon />
                    {totalUniqueItems > 0 && (
                        <span
                            className="absolute -bottom-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-lg border-2 border-white"
                            data-testid="cart-item-count"
                        >
                            {totalUniqueItems}
                        </span>
                    )}
                </div>
            </div>
        );

    if (isEmpty && isOpen)
        return (
            <div data-testid="cart-overlay">
                <div data-testid='cart-btn' className='flex row absolute' onClick={() => setIsOpen(false)}>
                    <CartIcon />
                    <p>Your cart is empty</p>
                </div>
            </div>
        );

    // console.log("First Render:", isFirstRender.current)

    // If cart is not empty and is open and is first render, close it
    if ((isOpen && !isEmpty) && isFirstRender.current) {
        setIsOpen(false);
        console.log("Should not be visible")
    }

    return (
        <>
            <div className="fixed inset-0 bg-black opacity-50 z-40" data-testid="cart-overlay" onClick={() => setIsOpen(false)} />
            <div data-testid='cart-btn'
                className="cart-container border border-dashed border-gray-300 p-5 w-[400px] my-5 mx-auto bg-neutral-600 absolute z-50"
            >
                <div className="cart-header border-b border-dashed border-gray-300 pb-2.5 mb-5 flex row justify-between">
                    <h2 className="m-0 text-xl font-bold" data-testid='cart-total'>
                        My Cart, {totalUniqueItems === 1 && items[0]?.quantity === 1
                            ? '1 item'
                            : `${items.reduce((acc, item) => acc + (item.quantity ?? 0), 0)} items`}
                    </h2>
                    <button className="p-2" onClick={() => setIsOpen(false)}>X</button>
                </div>

                <ul className="list-none p-0">
                    {items.map((item) => (
                        <li
                            key={item.id}
                            className="flex flex-col items-start mb-5 border-b border-dashed border-gray-200 pb-5 last:border-b-0 last:mb-0 last:pb-0"
                        >
                            <div className="item-details w-full mb-2">
                                <h3 className="m-0 text-lg">{item.name}</h3>
                                <p className="my-1 text-xl">${(item.price ?? 0).toFixed(2)}</p>
                                {item.attributes && Object.keys(item.attributes).length > 0 && (
                                    <div className="flex flex-col gap-1">
                                        <p>Attributes:</p>
                                        {item.allAttributes.map((attribute: any) => (
                                            <div key={attribute.id} data-testid={"cart-attribute-" + attribute.id}>
                                                <p className="font-semibold">{attribute.id}</p>
                                                {attribute.items && attribute.items.length > 0 && (
                                                    <CartAttributeSelector
                                                        options={attribute.items.map((item: any) => item.displayValue)}
                                                        trueValue={attribute.items.map((item: any) => item.value)}
                                                        attributeName={attribute.id}
                                                        brand={item.brand}
                                                        selectedValue={item.attributes[attribute.id]}
                                                    />
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="item-quantity-controls flex flex-row gap-2 items-center mb-2">
                                <p className='font-bold'>Quantity:</p>
                                <button
                                    onClick={() =>
                                        updateItemQuantity(item.id, (item.quantity ?? 0) - 1)
                                    }
                                    className="border border-gray-300 bg-red-700 font-black w-7 h-7 rounded text-xl cursor-pointer flex justify-center items-center" data-testid='cart-item-amount-decrease'
                                >
                                    -
                                </button>
                                <span className="my-2.5 text-xl" data-testid='cart-item-amount'>{item.quantity}</span>
                                <button
                                    onClick={() =>
                                        updateItemQuantity(item.id, (item.quantity ?? 0) + 1)
                                    }
                                    className="border border-gray-300 bg-green-700 font-black w-7 h-7 rounded text-xl cursor-pointer flex justify-center items-center" data-testid='cart-item-amount-increase'
                                >
                                    +
                                </button>
                                <button onClick={() => removeItem(item.id)} className="sr-only">&times;</button>
                            </div>

                            <div className="item-image w-full flex justify-center">
                                <img
                                    src={item.image}
                                    alt={item.name}
                                    className="w-24 h-24 object-cover rounded"
                                />
                            </div>
                        </li>
                    ))}
                </ul>

                <div className="cart-summary flex justify-between items-center border-t border-dashed border-gray-300 pt-3.5 mt-5">
                    <span className="text-xl font-bold">Total</span>
                    <span className="text-xl font-bold" data-testid='cart-total'>${totalPrice.toFixed(2)}</span>
                </div>

                <button className="empty-cart-button text-white border-none px-7 py-3.5 rounded text-lg w-full mt-5 transition-opacity bg-red-500 cursor-pointer hover:opacity-90" onClick={() => emptyCart()}>EMPTY CART</button>

                <button
                    className={`place-order-button text-white border-none px-7 py-3.5 rounded text-lg w-full mt-5 transition-opacity ${items.length <= 0
                            ? 'bg-gray-400 cursor-not-allowed opacity-60'
                            : 'bg-[#5cb85c] cursor-pointer hover:opacity-90'
                        }`}
                    onClick={() => {
                        if (items.length > 0) {
                            PlaceOrder(items);
                            emptyCart();
                        }
                    }}
                    disabled={items.length <= 0}
                >
                    PLACE ORDER
                </button>
            </div>
        </>
    );
}

export const Route = createRootRoute({
    component: () => {
        const { categories, loading } = useCategories();

        return (
            <>
            <div className="p-2 flex justify-between w-full bg-neutral-600 px-16 top-0 fixed">
                <div className="flex gap-2 flex-1">
                <Link
                    to="/"
                    activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold border-b-2 border-white' }}
                    inactiveProps={{ 'data-testid': 'category-link' }}
                >
                    Home
                </Link>{' '}
                {!loading && categories.map((name) => (
                    <Link
                    to="/$category"
                    params={{ category: name }}
                    key={name}
                    activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold border-b-2 border-white' }}
                    inactiveProps={{ 'data-testid': 'category-link' }}
                    >
                    {startCase(camelCase(name))}
                    </Link>
                ))}
                </div>
                
                <div className="flex-1 flex justify-center">
                <BagIcon />
                </div>
                
                <div className="flex-1 flex justify-end">
                <Cart />
                </div>
            </div>
            <hr />
            <Outlet />
            <TanStackRouterDevtools />
            </>
        );
    },
})
