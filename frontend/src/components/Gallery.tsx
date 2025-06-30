import React, { useState } from 'react';

interface GalleryProps {
  images: string[];
}

const Gallery: React.FC<GalleryProps> = ({ images }) => {
  if (!images || images.length === 0) return null;

  const [hoveredIndex, setHoveredIndex] = useState<number | null>(null);
  const [mainIndex, setMainIndex] = useState<number>(0);

  // Show hovered image if any, otherwise show the mainIndex
  const displayIndex = hoveredIndex !== null ? hoveredIndex : mainIndex;

  const handlePrev = () => {
    setMainIndex((prev) => (prev - 1 + images.length) % images.length);
    setHoveredIndex(null);
  };

  const handleNext = () => {
    setMainIndex((prev) => (prev + 1) % images.length);
    setHoveredIndex(null);
  };

  return (
    <div className="flex flex-row gap-4 items-center" data-testid='product-gallery'>
      <button
        aria-label="Previous image"
        className="p-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors flex justify-center items-center text-black"
        onClick={handlePrev}
        style={{ height: '2rem', width: '2rem' }}
      >
        &#60;
      </button>
      <img
        src={images[displayIndex]}
        alt="Main product"
        className="w-96 h-96 object-cover rounded-lg shadow-lg"
      />
      <button
        aria-label="Next image"
        className="p-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors flex justify-center items-center text-black"
        onClick={handleNext}
        style={{ height: '2rem', width: '2rem' }}
      >
        &#62;
      </button>
      <div
        className="flex flex-col gap-2 ml-4"
        onMouseLeave={() => setHoveredIndex(null)}
      >
        {images.map((img, idx) => (
          idx !== 0 && (
            <img
              key={idx}
              src={img}
              alt={`Product thumbnail ${idx + 1}`}
              className="w-24 h-24 object-cover rounded-md shadow cursor-pointer transition-transform duration-200 hover:scale-105"
              onMouseEnter={() => setHoveredIndex(idx)}
              onClick={() => { setMainIndex(idx); setHoveredIndex(null); }}
              style={{ border: displayIndex === idx ? '2px solid #4F46E5' : 'none' }}
            />
          )
        ))}
      </div>
    </div>
  );
};

export default Gallery; 