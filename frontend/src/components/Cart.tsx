// components/Cart.tsx
import { useState, useEffect, useRef } from 'react'
import { useCart} from 'react-use-cart'
import CartIcon from '../icons/CartIcon'
import CartEmpty from '../icons/CartEmpty'
import logger from './logger'
import CartItem from './CartItem';
import { placeOrder } from '../services/orderService'

interface CartProps {
    initialOpen?: boolean;
}

export default function Cart({ initialOpen = false }: CartProps) {
    const { isEmpty, totalUniqueItems, items, emptyCart } = useCart();

    const [isOpen, setIsOpen] = useState(initialOpen);
    const isFirstRender = useRef(true);

    // Effect to open cart when totalUniqueItems changes (i.e., an item is added)
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        if (!isEmpty && !isOpen) {
            setIsOpen(true);
            logger.info('Cart opened')
            logger.info(items)
        }
    }, [totalUniqueItems]);

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

    const handlePlaceOrder = async () => {
        if (items.length > 0) {
            await placeOrder(items);
            emptyCart();
        }
    };

    // If cart is not empty and is open and is first render, close it
    if ((isOpen && !isEmpty) && isFirstRender.current) {
        setIsOpen(false);
        console.log("Should not be visible")
    }

    if (isEmpty && !isOpen) {
        return (
            <div data-testid='cart-btn' className='flex row' onClick={() => setIsOpen(true)}>
                <CartEmpty />
            </div>
        );
    }

    if (!isEmpty && !isOpen) {
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
    }

    if (isEmpty && isOpen) {
        return (
            <div data-testid="cart-overlay">
                <div data-testid='cart-btn' className='flex row absolute' onClick={() => setIsOpen(false)}>
                    <CartIcon />
                    <p>Your cart is empty</p>
                </div>
            </div>
        );
    }

    return (
        <>
            <div className="fixed inset-0 bg-black opacity-50 z-0" data-testid="cart-overlay" onClick={() => setIsOpen(false)} />
            <div data-testid='cart-btn'
                className="cart-container p-5 w-[400px] my-5 mx-auto bg-neutral-600 absolute z-50"
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
                        <CartItem key={item.id} item={item} />
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
                    onClick={handlePlaceOrder}
                    disabled={items.length <= 0}
                >
                    PLACE ORDER
                </button>
            </div>
        </>
    );
}