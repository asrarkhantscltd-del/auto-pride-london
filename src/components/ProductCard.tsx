"use client";

import { motion } from "framer-motion";

export interface Product {
  id: string | number;
  image: string;
  name: string;
  category: string;
  price: number;
}

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  return (
    <motion.div
      className="rounded-2xl bg-[#18181C] overflow-hidden cursor-pointer"
      whileHover={{
        y: -6,
        scale: 1.03,
        boxShadow: "0 0 28px 3px rgba(0, 255, 204, 0.3)",
      }}
      transition={{ type: "spring", stiffness: 300, damping: 20 }}
    >
      <div className="relative h-40 w-full bg-black/40">
        <img
          src={product.image}
          alt={product.name}
          className="h-full w-full object-cover"
        />
      </div>

      <div className="p-4">
        <span className="text-xs uppercase tracking-wide text-gray-500">
          {product.category}
        </span>
        <h3 className="text-base font-semibold text-white mt-1 mb-3">
          {product.name}
        </h3>
        <span className="text-lg font-bold text-[#00FFCC]">
          £{product.price.toLocaleString()}
        </span>
      </div>
    </motion.div>
  );
}
