const SizeSelector = ({sizes, test, name, selectedSize, onSizeChange}: {sizes: string[], test: string[], name: string, selectedSize: string, onSizeChange: (size: string) => void}) => {

  const handleSizeClick = (size: string) => {
    onSizeChange(size);
  };

  const isHexColor = (str: string) => /^#([0-9A-Fa-f]{3}){1,2}$/.test(str);

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
              backgroundColor: isHexColor(test[index])
                ? test[index]
                : (selectedSize === size ? 'black' : 'white'),
              color: isHexColor(test[index])
                ? ((test[index].toLowerCase() === '#000' || test[index].toLowerCase() === '#000000') ? 'white' : 'black')
                : (selectedSize === size ? 'white' : 'black'),
              opacity: isHexColor(test[index])
                ? (selectedSize === size ? 1 : 0.5)
                : (selectedSize === '' ? 0.7 : 1)
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
