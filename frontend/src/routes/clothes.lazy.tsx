import ProductCard from '../components/ProductCard'
import { useQuery } from '@tanstack/react-query'
import { createLazyFileRoute, Link } from '@tanstack/react-router'
import '../app.css'

export const Route = createLazyFileRoute("/clothes")({
    component: ClothesIndex,
})

async function fetchClothesProducts() {
    const response = await fetch(import.meta.env.VITE_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: `
        {
            getProductsByCategory(category: "clothes") {
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
            `,
        })
    })
    return response.json()
}

function ClothesIndex() {
    const { data, isLoading, error } = useQuery({
        queryKey: ['clothesProducts'],
        queryFn: fetchClothesProducts
    })

    if (isLoading) return <div>Loading clothes...</div>;
    if (error) return <div>Error fetching clothes: {error.message}</div>;

    const products = data?.data?.getProductsByCategory || [];

    return (
        <>
            <h1 className="text-3xl font-bold text-center my-4">Clothes Products</h1>
            <main className="flex flex-row gap-4 flex-wrap justify-center">
                {products.length === 0 && <div>No products found for the "Clothes" category.</div>}
                {products.map((product: any) => (
                    <div key={product.id}>
                        <Link
                            to="/product/$productId"
                            params={{ productId: product.id }}
                            data-testid={`product-${product.name.toLowerCase().replace(/ /g, '-')}`}
                        >
                            <ProductCard {...product} />
                        </Link>
                    </div>
                ))}
            </main>
        </>
    )
} 