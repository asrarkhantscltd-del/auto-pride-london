"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, ChevronLeft, ChevronRight } from "lucide-react";

interface CarGalleryProps {
  images: string[];
  alt: string;
}

export default function CarGallery({ images, alt }: CarGalleryProps) {
  const [activeIndex, setActiveIndex] = useState<number | null>(null);

  const showPrev = () => {
    setActiveIndex((prev) =>
      prev === null ? null : (prev - 1 + images.length) % images.length
    );
  };

  const showNext = () => {
    setActiveIndex((prev) => (prev === null ? null : (prev + 1) % images.length));
  };

  return (
    <>
      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        {images.map((src, index) => (
          <motion.button
            key={src + index}
            onClick={() => setActiveIndex(index)}
            className="relative h-28 sm:h-36 rounded-xl overflow-hidden bg-[#18181C] cursor-pointer"
            whileHover={{ scale: 1.03 }}
          >
            <img
              src={src}
              alt={`${alt} photo ${index + 1}`}
              className="h-full w-full object-cover"
            />
          </motion.button>
        ))}
      </div>

      <AnimatePresence>
        {activeIndex !== null && (
          <motion.div
            className="fixed inset-0 z-[100] bg-black/90 flex items-center justify-center px-4"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setActiveIndex(null)}
          >
            <button
              onClick={() => setActiveIndex(null)}
              className="absolute top-5 right-5 text-white/80 hover:text-white"
              aria-label="Close"
            >
              <X className="w-8 h-8" />
            </button>

            <button
              onClick={(e) => {
                e.stopPropagation();
                showPrev();
              }}
              className="absolute left-4 sm:left-8 text-white/80 hover:text-white"
              aria-label="Previous photo"
            >
              <ChevronLeft className="w-10 h-10" />
            </button>

            <motion.img
              key={activeIndex}
              src={images[activeIndex]}
              alt={`${alt} photo ${activeIndex + 1}`}
              className="max-h-[85vh] max-w-full rounded-xl object-contain"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              onClick={(e) => e.stopPropagation()}
            />

            <button
              onClick={(e) => {
                e.stopPropagation();
                showNext();
              }}
              className="absolute right-4 sm:right-8 text-white/80 hover:text-white"
              aria-label="Next photo"
            >
              <ChevronRight className="w-10 h-10" />
            </button>

            <div className="absolute bottom-6 text-white/60 text-sm">
              {activeIndex + 1} / {images.length}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
