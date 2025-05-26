import { useState } from 'react'
import ProductCard from '../components/ProductCard'
import { useQuery } from '@tanstack/react-query'
import Cart from '../components/Cart'
import '../app.css'
import { createLazyFileRoute, Link } from '@tanstack/react-router'

export const Route  = createLazyFileRoute("/")({
  component: Index,
})

async function fetchProductsCards() {
  const response = await fetch(`http://localhost:8000/graphql`, {
    method: 'POST',
    body: JSON.stringify({query: `
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

async function fetchCategories() {
  const response = await fetch(`http://localhost:8000/graphql`, {
    method: 'POST',
    body: JSON.stringify({query: `{ getCategories { name } }`})
  })
  return response.json()
}

function Index() {
const [selectedCategory, setSelectedCategory] = useState('all')

  const { data, isLoading, error } = useQuery({
    queryKey: ['products'],
    queryFn: fetchProductsCards
  })

    const { data: categories, isLoading: categoriesLoading, error: categoriesError } = useQuery({
        queryKey: ['categories'],
        queryFn: fetchCategories
    })

    return (
        <>
        <nav className="flex flex-row gap-4">
            {categoriesLoading && <div>Loading...</div>}
            {categoriesError && <div>Error: {categoriesError.message}</div>}
            {categories &&
                categories.data.getCategories.map((category: any) => (
                    <a key={category.name} data-testid='category-link' href={`#${category.name}`} onClick={() => setSelectedCategory(category.name)}>{category.name}</a>
                ))
            }
            <Cart />
        </nav>
        <main className="flex flex-row gap-4 flex-wrap justify-center">
            {isLoading && <div>Loading...</div>}
            {error && <div>Error: {error.message}</div>}
            {data &&
                data.data.getProducts.map((product: any) => (
                <Link
                to="/product/$productId"
                key={product.id}
                params={{productId: product.id}}
                >
                    <ProductCard {...product} />
                </Link>
                ))
            }
        </main>
        </>
    )
}

// export default App
export default Route
