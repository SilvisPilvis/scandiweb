import { useState } from "react";

const SizeSelector = ({sizes, test, name}: {sizes: string[], test: string[], name: string}) => {
  const [selectedSize, setSelectedSize] = useState('S'); // Default to 'S' as in the image

  const handleSizeClick = (size: string) => {
    setSelectedSize(size);
  };

  return (
    <div>
      <div style={{ display: 'flex', gap: '10px' }}>
        {sizes.map((size, index) => (
          <button
            data-testid={"product-attribute-" + name.toLowerCase() + "-" + test[index]}
            key={size}
            onClick={() => handleSizeClick(size)}
            style={{
              padding: '10px 20px',
              border: '1px solid black',
              backgroundColor: selectedSize === size ? 'black' : 'white',
              color: selectedSize === size ? 'white' : 'black',
              cursor: 'pointer',
            }}
          >
            {size}
          </button>
        ))}
      </div>
    </div>
  );
};

export default SizeSelector;
