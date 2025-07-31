import { kebabCase } from 'lodash';

interface CartAttributeSelectorProps {
  attributeName: string;
  options: string[];
  trueValue: string[];
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
  trueValue
}) => {
  const attributeKebab = kebabCase(attributeName);

  if (brand.toLowerCase().includes("goose")) {
    options = filterArrayString(options)
  } else if (brand.toLowerCase().includes("nike")) {
    options = filterArrayNumeric(options)
  }

  // Helper function to check if a string is a hex color
  const isHexColor = (color: string): boolean => {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color);
  };

  return (
    <div data-testid={`cart-item-attribute-${attributeKebab}`}>
      <div style={{ display: 'flex', gap: '10px' }}>
        {options.map((option, index) => {
          const optionKebab = kebabCase(option);
          const isSelected = selectedValue === option;
          const isColor = trueValue && trueValue[index] ? isHexColor(trueValue[index]) : false;
          const colorValue = isColor && trueValue ? trueValue[index] : '';
          
          return (
            <button
              key={option}
              data-testid={`cart-item-attribute-${attributeKebab}-${optionKebab}${isSelected ? '-selected' : ''}`}
              className='p-2 rounded-md border border-gray-300 bg-white font-semibold'
              style={{
                backgroundColor: isColor ? colorValue : (isSelected ? 'black' : 'white'),
                color: isColor 
                  ? ((colorValue?.toLowerCase() === '#000' || colorValue?.toLowerCase() === '#000000') ? 'white' : 'black')
                  : (isSelected ? 'white' : 'black'),
                opacity: isSelected ? 1 : 0.5,
                cursor: 'not-allowed',
                minWidth: isColor ? '40px' : 'auto',
                height: isColor ? '40px' : 'auto'
              }}
              disabled
            >
              {!isColor && option}
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default CartAttributeSelector; 