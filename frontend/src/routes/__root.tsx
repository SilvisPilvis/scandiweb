import { createRootRoute, Link, Outlet } from '@tanstack/react-router'
import { TanStackRouterDevtools } from '@tanstack/react-router-devtools'
import { useCart } from 'react-use-cart'
import CartIcon from '../icons/CartIcon'
import { useState } from 'react'
// import SizeSelector from '../components/SizeSelector'
import CartAttributeSelector from '../components/CartAttributeSelector'

async function PlaceOrderMutation(items: string) {
    console.log(items);
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
                    items: items
                }
            }
        })
    })
    console.log(response.json());
    // return response.json()
}

async function PlaceOrder(items: string) {
    await PlaceOrderMutation(items);
    alert("Order placed");
}

function Cart() {
    const { isEmpty, totalUniqueItems, items, updateItemQuantity, removeItem, emptyCart } =
        useCart();

    const [isOpen, setIsOpen] = useState(false);

    // Calculate total price
    const totalPrice = items.reduce(
        (acc, item) => acc + (item.price ?? 0) * (item.quantity ?? 0),
        0,
    );

    if (isEmpty && !isOpen)
        return (
            <div data-testid='cart-btn' className='flex row m-6'>
                <CartIcon />
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

    return (
        <>
            <div className="fixed inset-0 bg-black opacity-50 z-40" data-testid="cart-overlay" onClick={() => setIsOpen(false)} />
            <div data-testid='cart-btn'
                className="cart-container border border-dashed border-gray-300 p-5 w-[400px] my-5 mx-auto rounded-lg bg-neutral-600 relative z-50"
            >
                <div className="cart-header border-b border-dashed border-gray-300 pb-2.5 mb-5 flex row justify-between">
                    <h2 className="m-0 text-xl font-bold" data-testid='cart-total'>
                        My Cart, {totalUniqueItems} {totalUniqueItems === 1 ? 'item' : 'items'}
                    </h2>
                    <button className="p-2" onClick={() => setIsOpen(false)}>X</button>
                </div>

                <ul className="list-none p-0">
                    {items.map((item) => (
                        <li
                            key={item.id}
                            className="flex items-center mb-5 border-b border-dashed border-gray-200 pb-5 last:border-b-0 last:mb-0 last:pb-0"
                        >
                            <div className="item-details flex-grow pr-2.5">
                                <h3 className="m-0 text-lg">{item.name}</h3>
                                <p className="my-1 text-xl">${(item.price ?? 0).toFixed(2)}</p>
                                {item.attributes && item.attributes.length > 0 && (
                                    <div className="flex flex-col gap-1">
                                        {item.attributes.map((attribute: any) => (
                                            <div key={attribute.id} data-testid={"cart-attribute-" + attribute.id}>
                                                <p className="font-semibold">{attribute.id}</p>
                                                {attribute.items && attribute.items.length > 0 && (
                                                    <CartAttributeSelector
                                                        options={attribute.items.map((item: any) => item.displayValue)}
                                                        attributeName={attribute.id}
                                                        brand={item.brand}
                                                    />
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="item-quantity-controls flex flex-row gap-2 items-center mr-4">
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

                            <div className="item-image">
                                <img
                                    src={item.image}
                                    alt={item.name}
                                    className="w-24 h-24 object-cover"
                                />
                            </div>
                        </li>
                    ))}
                </ul>

                <div className="cart-summary flex justify-between items-center border-t border-dashed border-gray-300 pt-3.5 mt-5">
                    <span className="text-xl font-bold">Total</span>
                    <span className="text-xl font-bold" data-testid='cart-total'>${totalPrice.toFixed(2)}</span>
                </div>

                <button
                    className={`place-order-button text-white border-none px-7 py-3.5 rounded text-lg w-full mt-5 transition-opacity ${items.length <= 0
                            ? 'bg-gray-400 cursor-not-allowed opacity-60'
                            : 'bg-[#5cb85c] cursor-pointer hover:opacity-90'
                        }`}
                    onClick={() => {
                        if (items.length > 0) {
                            PlaceOrder(JSON.stringify(items));
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
    component: () => (
        <>
            <div className="p-2 flex gap-2">
                <Link
                    to="/"
                    activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold' }}
                    inactiveProps={{ 'data-testid': 'category-link' }}
                >
                    All
                </Link>{' '}
                <Link
                    to="/clothes"
                    activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold' }}
                    inactiveProps={{ 'data-testid': 'category-link' }}
                >
                    Clothes
                </Link>{' '}
                <Link
                    to="/tech"
                    activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold' }}
                    inactiveProps={{ 'data-testid': 'category-link' }}
                >
                    Tech
                </Link>{' '}
                <Cart />
            </div>
            <hr />
            <Outlet />
            <TanStackRouterDevtools />
        </>
    ),
})
