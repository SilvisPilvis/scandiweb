import ProductCard from '../components/ProductCard'
import { useQuery } from '@tanstack/react-query'
import { createLazyFileRoute, Link } from '@tanstack/react-router'
import '../app.css'

export const Route = createLazyFileRoute("/")({
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
                in_stock
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

    return (
        <>
            <h1 className="text-3xl font-bold text-center my-4">All Products</h1>
            <main className="flex flex-row gap-4 flex-wrap justify-center">
                {isLoading && <div>Loading...</div>}
                {error && <div>Error: {error.message}</div>}
                {data &&
                    data.data.getProducts.map((product: any) => (
                        <Link
                            to="/product/$productId"
                            key={product.id}
                            params={{ productId: product.id }}
                            data-testid={`product-${product.name.toLowerCase().replace(/ /g, '-')}`}
                        >
                            <ProductCard {...product} attributes={product.attributes} brand={product.brand} />
                        </Link>
                    ))
                }
            </main>
        </>
    )
}

// export default App
export default Route
