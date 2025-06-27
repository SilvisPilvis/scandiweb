import { useCart } from 'react-use-cart'
import { kebabCase } from 'lodash'

const ProductCard = ({id, name, inStock, gallery, prices}: {id: string, name: string, inStock: boolean, gallery: string[], prices: any[]}) => {
  const { addItem } = useCart();
  return (
    <>
    {inStock ? (
    <div className="flex flex-col items-start justify-center gap-4 max-w-sm" data-testid={"product-" + kebabCase(name)}>
        <img src={gallery[0]} alt={name} className="min-w-72 max-w-72 object-contain rounded-lg aspect-square" />
        <h3 className="text-lg font-bold">{name}</h3>
        <p className="text-lg font-bold">{prices[0].currency.symbol}{prices[0].amount}</p>
        <button onClick={() => addItem({id, name, price: prices[0].amount, image: gallery[0]})}>Add to cart</button>
        {/* <div className="text-sm text-white">{parse(description)}</div> / */}
    </div>
    ) : (
        <div className="flex flex-col items-start justify-center gap-4 max-w-sm">
            <div className="relative">
                <img src={gallery[0]} alt={name} className="min-w-72 max-w-72 object-contain rounded-lg aspect-square opacity-50" />
                <p className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-2xl font-bold text-red-700 w-full text-center">
                    OUT OF STOCK
                </p>
            </div>
            <h3 className="text-lg font-bold">{name}</h3>
            <p className="text-lg font-bold">{prices[0].currency.symbol}{prices[0].amount}</p>
        </div>
    )}
    </>
  )
}

export default ProductCard