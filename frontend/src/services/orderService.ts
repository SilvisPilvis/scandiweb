// services/orderService.ts
import type { Item } from 'react-use-cart'

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

export async function placeOrder(items: Item[]) {
    const response = await PlaceOrderMutation(items);
    if (response.errors) {
        alert("Error placing order");
        console.error("Error:", response.errors[0].message)
        return;
    }
    alert("Order placed");
}