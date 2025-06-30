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
            className="p-2 rounded-md cursor-pointer border border-gray-300 bg-white font-semibold"
            style={{
              backgroundColor: selectedSize === size ? 'black' : 'white',
              color: selectedSize === size ? 'white' : 'black',
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
