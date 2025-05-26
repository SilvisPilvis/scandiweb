import { createFileRoute } from '@tanstack/react-router'
import parse from 'html-react-parser'
import { useQuery } from '@tanstack/react-query'

export const Route = createFileRoute('/product/$productId')({
  component: Product,
  loader: async ({params}) => {
    const {productId} = params
    // const {data} = await fetchProduct(productId)
    // return data
    return {
        productId
    }
  }
})

async function fetchProduct(id: string) {
    const response = await fetch(`http://localhost:8000/graphql`, {
        method: 'POST',
        body: JSON.stringify({query: `
            {
                getProduct(id: "${id}") {
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
            `
        })
    })
    return response.json()
}

function AddToCart() {
    console.log('Add to Cart')
}

function Product() {
    const {productId} = Route.useLoaderData()

    const { data, isLoading, error } = useQuery({
        queryKey: ['product', productId],
        queryFn: () => fetchProduct(productId)
    })

    return (
      <div>
        {isLoading && <div>Loading...</div>}
        {error && <div>Error: {error.message}</div>}
        {data &&
        <>
            <div className="flex flex-row gap-4 text-xl">
                <img src={data.data.getProduct.gallery[0]} alt={data.data.getProduct.name} />
                <div>
                <p className="text-2xl font-bold">{data.data.getProduct.name}</p>
                <div>{parse(data.data.getProduct.description)}</div>
                <p>Price: </p>
                <p>{data.data.getProduct.prices[0].currency.symbol}{data.data.getProduct.prices[0].amount}</p>
                <button className="bg-green-500 text-white rounded-md p-2" onClick={AddToCart}>Add to Cart</button>
                <p>Brand: {data.data.getProduct.brand}</p>
                <p>Stock: {data.data.getProduct.inStock ? 'In Stock' : 'Out of Stock'}</p>
                </div>
            </div>
        </>
        }
      </div>
    )
  }
