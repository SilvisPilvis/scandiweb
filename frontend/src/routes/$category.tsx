import { createFileRoute, Link } from '@tanstack/react-router'
import { useQuery } from '@tanstack/react-query'
import ProductCard from '../components/ProductCard'
import {startCase, camelCase} from 'lodash';

export const Route = createFileRoute('/$category')({
  component: CategoryIndex,
  loader: async ({params}) => {
    const {category} = params
    return {
        category
    }
  }
})

const wildcardMatches = [
  "",
  "/",
  "all",
  "home",
  null,
  undefined
]

async function fetchProductsByCategory(category: string) {
  if (wildcardMatches.includes(category)) {
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

  const response = await fetch(import.meta.env.VITE_API_URL, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({
          query: `
      {
          getProductsByCategory(category: "${category}") {
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
          `,
      })
  })
  return response.json()
}

function CategoryIndex() {
  const { category } = Route.useLoaderData()
  const { data, isLoading, error } = useQuery({
    queryKey: ['productsByCategory', category],
    queryFn: () => fetchProductsByCategory(category)
  })

  console.log(`Category is: ${category}`)

  if (isLoading) return <div>Loading products...</div>;
  if (error) return <div>Error fetching products: {error.message}</div>;

  let products;

  if (wildcardMatches.includes(category)){
    products = data?.data?.getProducts || [];
  } else {
    products = data?.data?.getProductsByCategory || [];
  }


    return (
    <>
        <h1 className="text-3xl font-bold text-center my-4">{ startCase(camelCase(category)) } Products</h1>
        <main className="flex flex-row gap-4 flex-wrap justify-center">
            {products.length === 0 && <div>No products found for the in the { startCase(camelCase(category)) } category.</div>}
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
