"use client";

import { motion } from "framer-motion";
import ProductCard, { Product } from "./ProductCard";

interface ProductsGridProps {
  products: Product[];
}

const containerVariants = {
  hidden: {},
  visible: {
    transition: {
      staggerChildren: 0.08,
    },
  },
};

const cardVariants = {
  hidden: { opacity: 0, y: 30 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.4, ease: "easeOut" as const },
  },
};

export default function ProductsGrid({ products }: ProductsGridProps) {
  return (
    <motion.div
      className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 p-6 bg-[#0F0F12]"
      variants={containerVariants}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, amount: 0.2 }}
    >
      {products.map((product) => (
        <motion.div key={product.id} variants={cardVariants}>
          <ProductCard product={product} />
        </motion.div>
      ))}
    </motion.div>
  );
}
