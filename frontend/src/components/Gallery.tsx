import React, { useState } from 'react';

interface GalleryProps {
  images: string[];
}

const Gallery: React.FC<GalleryProps> = ({ images }) => {
  if (!images || images.length === 0) return null;

  const [hoveredIndex, setHoveredIndex] = useState<number | null>(null);

  // Show hovered image if any, otherwise show the first image
  const mainIndex = hoveredIndex !== null ? hoveredIndex : 0;

  return (
    <div className="flex flex-row gap-4" data-testid='product-gallery'>
      <img
        src={images[mainIndex]}
        alt="Main product"
        className="w-96 h-96 object-cover rounded-lg shadow-lg"
      />
      <div
        className="flex flex-col gap-2"
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
            />
          )
        ))}
      </div>
    </div>
  );
};

export default Gallery; 