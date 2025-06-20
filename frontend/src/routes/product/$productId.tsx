import { createFileRoute } from '@tanstack/react-router'
import parse from 'html-react-parser'
import { useQuery } from '@tanstack/react-query'
import { useCart } from 'react-use-cart'
import SizeSelector from '../../components/SizeSelector'
import Gallery from '../../components/Gallery'
import { kebabCase } from 'lodash'

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
            `
        })
    })
    return response.json()
}

function isNumeric(str: string) {
    if (typeof str != "string") return false // we only process strings!  
    return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
           !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
}

function filterArrayNumeric(arr: string[]): string[] {
    return arr.filter(str => {
        if (typeof str !== "string") return false;
        return !isNaN(Number(str));
    });
}

function filterArrayString(arr: string[]): string[] {
    return arr.filter(str => {
        if (typeof str !== "string") return false;
        return isNaN(Number(str));
    });
}

function Product() {
    const {productId} = Route.useLoaderData()

    const { data, isLoading, error } = useQuery({
        queryKey: ['product', productId],
        queryFn: () => fetchProduct(productId)
    })

    const { addItem } = useCart();

    return (
      <div>
        {isLoading && <div>Loading...</div>}
        {error && <div>Error: {error.message}</div>}
        {data &&
        <>
            <div className="flex flex-row gap-4 text-xl">
                {/* <img src={data.data.getProduct.gallery[0]} alt={data.data.getProduct.name} /> */}
                <Gallery images={data.data.getProduct.gallery} />
                <div>
                <p className="text-2xl font-bold">{data.data.getProduct.name}</p>
                <p>Price: </p>
                <p>{data.data.getProduct.prices[0].currency.symbol}{data.data.getProduct.prices[0].amount}</p>
                <div>
                    {data.data.getProduct.brand === 'Canada Goose' ? (
                        data.data.getProduct.attributes.map((attribute: any) => (
                            <div key={attribute.id} data-testid={"product-attribute-" + kebabCase(attribute.id)}>
                                <p key={attribute.id}>{attribute.id}</p>
                                <SizeSelector sizes={filterArrayString(attribute.items.map((item: any) => item.displayValue))} />
                            </div>
                        ))
                    ) : data.data.getProduct.brand.toLowerCase().includes('nike') ? (
                        data.data.getProduct.attributes.map((attribute: any) => (
                            <div key={attribute.id} data-testid={"product-attribute-" + kebabCase(attribute.id)}>
                                <p key={attribute.id}>{attribute.id}</p>
                                <SizeSelector sizes={filterArrayNumeric(attribute.items.map((item: any) => item.displayValue))} />
                            </div>
                        ))
                    ) : (
                        data.data.getProduct.attributes.map((attribute: any) => (
                            <div key={attribute.id} data-testid={"product-attribute-" + kebabCase(attribute.id)}>
                                {/* The name of the attribute */}
                                <p key={attribute.id}>{attribute.id}</p>
                                <SizeSelector sizes={attribute.items.map((item: any) => item.displayValue)} />
                            </div>
                        ))
                    )}
                </div>
                <button
                className="bg-green-500 text-white rounded-md p-2 mt-2"
                onClick={() => addItem({id: data.data.getProduct.id, name: data.data.getProduct.name, price: data.data.getProduct.prices[0].amount, image: data.data.getProduct.gallery[0], attributes: data.data.getProduct.attributes, brand: data.data.getProduct.brand})}
                data-testid='add-to-cart'
                >Add to Cart</button>
                <p>Brand: {data.data.getProduct.brand}</p>
                <p>Stock: {data.data.getProduct.inStock ? 'In Stock' : 'Out of Stock'}</p>
                <div data-testid='product-description'>{parse(data.data.getProduct.description)}</div>
                </div>
            </div>
        </>
        }
      </div>
    )
  }
