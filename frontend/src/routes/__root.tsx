import { createRootRoute, Link, Outlet } from '@tanstack/react-router'
import { TanStackRouterDevtools } from '@tanstack/react-router-devtools'
import { useCart } from 'react-use-cart'
import CartIcon from '../icons/CartIcon'
import { useState } from 'react'

// function Cart() {
//     const {
//       isEmpty,
//       totalUniqueItems,
//       items,
//       updateItemQuantity,
//       removeItem,
//     } = useCart();
//
//     const [isOpen, setIsOpen] = useState(false);
//
//     if (isEmpty && !isOpen) return (
//         <>
//         0 x <CartIcon />;
//         </>
//     )
//
//     if (!isEmpty && !isOpen) return (
//         <div onClick={() => setIsOpen(true)}>
//             {totalUniqueItems} x <CartIcon />
//         </div>
//     )
//
//     return (
//       <>
//         <h1>Cart ({totalUniqueItems})</h1>
//
//         <ul>
//           {items.map((item) => (
//             <li key={item.id}>
//               {item.quantity} x {item.name} &mdash;
//               <button
//                 onClick={() => updateItemQuantity(item.id, (item.quantity ?? 0) - 1)}
//               >
//                 -
//               </button>
//               <button
//                 onClick={() => updateItemQuantity(item.id, (item.quantity ?? 0) + 1)}
//               >
//                 +
//               </button>
//               <button onClick={() => removeItem(item.id)}>&times;</button>
//             </li>
//           ))}
//         </ul>
//       </>
//     );
// }

function Cart() {
    const { isEmpty, totalUniqueItems, items, updateItemQuantity, removeItem } =
        useCart();

    const [isOpen, setIsOpen] = useState(false);

    // Calculate total price
    const totalPrice = items.reduce(
        (acc, item) => acc + (item.price ?? 0) * (item.quantity ?? 0),
        0,
    );

    if (isEmpty && !isOpen)
        return (
            <>
                0 x <CartIcon />
            </>
        );

    if (!isEmpty && !isOpen)
        return (
            <div onClick={() => setIsOpen(true)}>
                {totalUniqueItems} x <CartIcon />
            </div>
        );

    return (
        <div
            className="cart-container border border-dashed border-gray-300 p-5 w-[400px] my-5 mx-auto rounded-lg"
        // Tailwind doesn't have a direct "dotted" border style for utility classes,
        // so using "dashed" which is visually similar to the image, or you can
        // use a custom CSS variable if you need exact dotted.
        >
            <div className="cart-header border-b border-dashed border-gray-300 pb-2.5 mb-5">
                <h2 className="m-0 text-xl font-bold" data-testid='cart-total'>My Bag, {totalUniqueItems} items</h2>
                {/* Optional: Add a close button if needed */}
                {/* <button onClick={() => setIsOpen(false)}>X</button> */}
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
                            {/* Assuming `item.selectedSize` and `item.availableSizes` */}
                            {item.availableSizes && item.availableSizes.length > 0 && (
                                <div className="mb-2.5">
                                    <p className="m-0 text-sm">Size:</p>
                                    <div className="flex gap-1.5">
                                        {item.availableSizes.map((size: string) => (
                                            <div
                                                key={size}
                                                className={`
                          border border-gray-300 px-2.5 py-1 rounded cursor-pointer text-sm min-w-[30px] text-center
                          ${item.selectedSize === size
                                                        ? "bg-black text-white"
                                                        : "bg-white text-black"
                                                    }
                        `}
                                            // In a real application, you'd want to update the item's size
                                            // onClick={() => updateItemSize(item.id, size)}
                                            >
                                                {size.toUpperCase()}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Assuming `item.selectedColor` and `item.availableColors` */}
                            {item.availableColors && item.availableColors.length > 0 && (
                                <div>
                                    <p className="m-0 text-sm">Color:</p>
                                    <div className="flex gap-1.5">
                                        {item.availableColors.map((color: string) => (
                                            <div
                                                key={color}
                                                className={`
                          w-6 h-6 rounded-full cursor-pointer
                          ${item.selectedColor === color ? "border-2 border-black" : "border border-gray-300"}
                        `}
                                                style={{ backgroundColor: color }} // Tailwind doesn't have dynamic background colors directly, so inline style is used here. For known colors, you could use JIT mode or add custom colors to tailwind.config.js
                                            // In a real application, you'd want to update the item's color
                                            // onClick={() => updateItemColor(item.id, color)}
                                            ></div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="item-quantity-controls flex flex-row gap-2 items-center mr-4">
                            <button
                                onClick={() =>
                                    updateItemQuantity(item.id, (item.quantity ?? 0) + 1)
                                }
                                className="border border-gray-300 bg-green-700 font-black w-7 h-7 rounded text-xl cursor-pointer flex justify-center items-center" data-testid='cart-item-amount-increase'
                            >
                                +
                            </button>
                            <span className="my-2.5 text-xl" data-testid='cart-item-amount'>{item.quantity}</span>
                            <button
                                onClick={() =>
                                    updateItemQuantity(item.id, (item.quantity ?? 0) - 1)
                                }
                                className="border border-gray-300 bg-red-700 font-black w-7 h-7 rounded text-xl cursor-pointer flex justify-center items-center" data-testid='cart-item-amount-decrease'
                            >
                                -
                            </button>
                            <button onClick={() => removeItem(item.id)} className="sr-only">&times;</button>
                            {/* The image doesn't show a dedicated remove button per item, so I've hidden it for now */}
                            {/* If you want to keep a hidden remove, you could add: */}
                            {/* <button onClick={() => removeItem(item.id)} className="sr-only">&times;</button> */}
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
                <span className="text-xl font-bold">${totalPrice.toFixed(2)}</span>
            </div>

            <button
                className="place-order-button bg-[#5cb85c] text-white border-none px-7 py-3.5 rounded cursor-pointer text-lg w-full mt-5 hover:opacity-90 transition-opacity"
                onClick={() => alert("Placing order...")} // Replace with actual order placement logic
            >
                PLACE ORDER
            </button>
        </div>
    );
}

export const Route = createRootRoute({
    component: () => (
        <>
            <div className="p-2 flex gap-2">
                <Link to="/" className="[&.active]:font-bold" data-testid='category-link'>
                    Home
                </Link>{' '}
                <Cart />
            </div>
            <hr />
            <Outlet />
            <TanStackRouterDevtools />
        </>
    ),
})
