import { kebabCase } from 'lodash';

interface CartAttributeSelectorProps {
  attributeName: string;
  options: string[];
  brand: string
  selectedValue: string
  onChange: (value: string) => void;
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
  onChange
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
              onClick={() => onChange(option)}
              data-testid={`cart-item-attribute-${attributeKebab}-${optionKebab}${isSelected ? '-selected' : ''}`}
              className='p-2 rounded-md cursor-pointer border border-gray-300 bg-white font-semibold'
              style={{
                backgroundColor: isSelected ? 'black' : 'white',
                color: isSelected ? 'white' : 'black',
              }}
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