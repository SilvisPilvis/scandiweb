const SizeSelector = ({sizes, test, name, selectedSize, onSizeChange}: {sizes: string[], test: string[], name: string, selectedSize: string, onSizeChange: (size: string) => void}) => {

  const handleSizeClick = (size: string) => {
    onSizeChange(size);
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
