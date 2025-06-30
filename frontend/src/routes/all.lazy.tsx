import ProductCard from '../components/ProductCard'
import { useQuery } from '@tanstack/react-query'
import { createLazyFileRoute, Link } from '@tanstack/react-router'
import { useCart } from 'react-use-cart'
import '../app.css'
import CartIcon from '../icons/CartIcon'

export const Route = createLazyFileRoute("/all")({
    component: Index,
})

async function fetchProductsCards() {
    const response = await fetch(import.meta.env.VITE_API_URL, {
        method: 'POST',
        body: JSON.stringify({
            query: `
        {
            getProducts {
                id
                name
                inStock
                gallery
                description
                prices {
                amount
                currency {
                    label
                    symbol
                }
                }
                attributes {
                id
                items {
                    id
                    displayValue
                    value
                }
                }
                brand
                category {
                name
                }
            }
        }
        `})
    })
    return response.json()
}

function Index() {
    const { data, isLoading, error } = useQuery({
        queryKey: ['products'],
        queryFn: fetchProductsCards
    })

    const { addItem } = useCart();

    return (
        <>
            <h1 className="text-3xl font-bold text-center my-4">All Products</h1>
            <main className="flex flex-row gap-4 flex-wrap justify-center">
                {isLoading && <div>Loading...</div>}
                {error && <div>Error: {error.message}</div>}
                {data &&
                    data.data.getProducts.map((product: any) => (
                        <>
                            <Link
                                to="/product/$productId"
                                key={product.id}
                                params={{ productId: product.id }}
                                data-testid={`product-${product.name.toLowerCase().replace(/ /g, '-')}`}
                            >
                                <ProductCard {...product} />
                            </Link>
                            <button
                            onClick={() => addItem({id: product.id, name: product.name, price: product.prices[0].amount, image: product.gallery[0], attributes: product.attributes, brand: product.brand})}
                            data-testid='add-to-cart'
                            ><CartIcon /></button>
                            {/* <CartIcon onClick={() => addItem(product)} /> */}
                        </>
                    ))
                }
            </main>
        </>
    )
}

// export default App
export default Route
