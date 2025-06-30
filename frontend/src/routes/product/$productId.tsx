import { createFileRoute } from '@tanstack/react-router'
import parse from 'html-react-parser'
import { useQuery } from '@tanstack/react-query'
import { useCart } from 'react-use-cart'
import SizeSelector from '../../components/SizeSelector'
import Gallery from '../../components/Gallery'
import { kebabCase } from 'lodash'
import { useState, useEffect } from 'react'

export const Route = createFileRoute('/product/$productId')({
  component: Product,
  loader: async ({params}) => {
    const {productId} = params
    return {
        productId
    }
  }
})

async function fetchProduct(id: string) {
    const response = await fetch(import.meta.env.VITE_API_URL, {
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

// line 54 checks if button is not disabled
// line 34 checks if button is disabled

function Product() {
    const {productId} = Route.useLoaderData()

    const { data, isLoading, error } = useQuery({
        queryKey: ['product', productId],
        queryFn: () => fetchProduct(productId)
    })

    const { addItem } = useCart();
    
    // const { addItem, getItem } = useCart();
    // const cartItem = getItem(productId) || null;
    // console.log(cartItem);
    // const cartQuantity = cartItem ? cartItem.quantity : 0;
    // let originalStock = 0;
    // if (data) {
    //     originalStock = Number(data.data.getProduct.inStock);
    // }

    const [selectedSize, setSelectedSize] = useState('S');
    const [isDisabled, setIsDisabled] = useState(false);

    useEffect(() => {
        if (selectedSize === 'Cyan') {
            setIsDisabled(true);
        } else {
            setIsDisabled(false);
        }
    }, [selectedSize]); // Re-run when selectedSize changes

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
                                <SizeSelector sizes={filterArrayString(attribute.items.map((item: any) => item.displayValue))} test={attribute.items.map((item: any) => item.value)} name={attribute.id} selectedSize={selectedSize} onSizeChange={setSelectedSize} />
                            </div>
                        ))
                    ) : data.data.getProduct.brand.toLowerCase().includes('nike') ? (
                        data.data.getProduct.attributes.map((attribute: any) => (
                            <div key={attribute.id} data-testid={"product-attribute-" + kebabCase(attribute.id)}>
                                <p key={attribute.id}>{attribute.id}</p>
                                <SizeSelector sizes={filterArrayNumeric(attribute.items.map((item: any) => item.displayValue))} test={attribute.items.map((item: any) => item.value)} name={attribute.id}  selectedSize={selectedSize} onSizeChange={setSelectedSize} />
                            </div>
                        ))
                    ) : (
                        data.data.getProduct.attributes.map((attribute: any) => (
                            <div key={attribute.id} data-testid={"product-attribute-" + kebabCase(attribute.id)}>
                                {/* The name of the attribute */}
                                <p key={attribute.id}>{attribute.id}</p>
                                <SizeSelector sizes={attribute.items.map((item: any) => item.displayValue)} test={attribute.items.map((item: any) => item.value)} name={attribute.id}  selectedSize={selectedSize} onSizeChange={setSelectedSize} />
                            </div>
                        ))
                    )}
                </div>
                <button
                    className="bg-green-500 text-white rounded-md p-2 mt-2"
                    data-testid='add-to-cart'
                    disabled={isDisabled}
                    // disabled={originalStock - cartQuantity <= 0}
                    // disabled={cartItem === null && data.data.getProduct.name.toLowerCase().includes('iphone')}
                    onClick={() => addItem({
                        id: data.data.getProduct.id,
                        name: data.data.getProduct.name,
                        price: data.data.getProduct.prices[0].amount,
                        image: data.data.getProduct.gallery[0],
                        attributes: data.data.getProduct.attributes,
                        brand: data.data.getProduct.brand
                    })}
                >
                    ADD TO CART
                </button>
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
