import { kebabCase } from 'lodash';

interface CartAttributeSelectorProps {
  attributeName: string;
  options: string[];
  brand: string
  selectedValue: string
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

const CartAttributeSelector: React.FC<CartAttributeSelectorProps> = ({
  attributeName,
  options,
  brand,
  selectedValue,
}) => {
  const attributeKebab = kebabCase(attributeName);

  if (brand.toLowerCase().includes("goose")) {
    options = filterArrayString(options)
  }else if (brand.toLowerCase().includes("nike")) {
    options = filterArrayNumeric(options)
  }

  return (
    <div data-testid={`cart-item-attribute-${attributeKebab}`}>
      <div style={{ display: 'flex', gap: '10px' }}>
        {options.map((option) => {
          const optionKebab = kebabCase(option);
          const isSelected = selectedValue === option;
          return (
            <button
              key={option}
              // Remove onClick to disable manual selection
              data-testid={`cart-item-attribute-${attributeKebab}-${optionKebab}${isSelected ? '-selected' : ''}`}
              className='p-2 rounded-md border border-gray-300 bg-white font-semibold'
              style={{
                backgroundColor: isSelected ? 'black' : 'white',
                color: isSelected ? 'white' : 'black',
                opacity: isSelected ? 1 : 0.5, // visually indicate disabled
                cursor: 'not-allowed',
              }}
              disabled
            >
              {option}
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default CartAttributeSelector; 