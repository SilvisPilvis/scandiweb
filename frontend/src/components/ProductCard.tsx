import { useCart } from 'react-use-cart'
import { kebabCase } from 'lodash'
import CartIcon from '../icons/CartIcon'
import { getCartItemId } from '../routes/__root'

const ProductCard = ({id, name, in_stock, gallery, prices, attributes, brand}: {id: string, name: string, in_stock: boolean, gallery: string[], prices: any[], attributes?: any, brand?: string}) => {
  const { addItem } = useCart();

  return (
    <>
    {in_stock ? (
    <div className="flex flex-col items-start justify-center gap-4 max-w-sm group hover:bg-gray-200 rounded-lg p-4" data-testid={"product-" + kebabCase(name)}>
        <div className="relative">
          <div className="w-72 h-72 overflow-hidden rounded-lg">
            <img 
              src={gallery[0]} 
              alt={name} 
              className="w-full h-full object-cover" 
            />
          </div>
          <button
            onClick={() => addItem({
                id: getCartItemId(id, attributes?.reduce((acc: any, attr: any) => ({
                    ...acc,
                    [attr.id]: attr.items[0]?.displayValue
                }), {})),
                name, 
                price: prices[0].amount, 
                image: gallery[0], 
                attributes: attributes?.reduce((acc: any, attr: any) => ({
                    ...acc,
                    [attr.id]: attr.items[0]?.displayValue
                }), {}), 
                allAttributes: attributes,
                brand
            })}
            data-testid='add-to-cart'
            className="absolute bottom-2 right-2 bg-green-500 rounded-md p-2 shadow-lg hover:bg-green-600 transition-colors opacity-0 group-hover:opacity-100 transition-opacity"
          >
            <CartIcon />
          </button>
        </div>
        <h3 className="text-lg font-bold">{name}</h3>
        <p className="text-lg font-bold">{prices[0].currency.symbol}{prices[0].amount}</p>
    </div>
    ) : (
        <div className="flex flex-col items-start justify-center gap-4 max-w-sm group hover:bg-gray-200 rounded-lg p-4" data-testid={"product-" + kebabCase(name)}>
            <div className="relative">
                <div className="w-72 h-72 overflow-hidden rounded-lg">
                  <img 
                    src={gallery[0]} 
                    alt={name} 
                    className="w-full h-full object-cover opacity-50" 
                  />
                </div>
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