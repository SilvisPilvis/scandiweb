// components/CartItem.tsx
import { useCart, type Item } from 'react-use-cart'
import CartAttributeSelector from './CartAttributeSelector'

interface CartItemProps {
    item: Item;
}

export default function CartItem({ item }: CartItemProps) {
    const { updateItemQuantity, removeItem } = useCart();

    return (
        <li className="flex flex-col items-start mb-5 border-b border-dashed border-gray-200 pb-5 last:border-b-0 last:mb-0 last:pb-0">
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
    );
}