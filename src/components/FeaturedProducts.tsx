"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import ProductCard, { Product } from "./ProductCard";
import { products as fallbackProducts } from "@/data/products";

export default function FeaturedProducts() {
  const [products, setProducts] = useState<Product[]>(
    fallbackProducts.slice(0, 4)
  );

  useEffect(() => {
    fetch("/backend/api/products.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load products");
        return res.json();
      })
      .then((data: Product[]) => setProducts(data.slice(0, 4)))
      .catch(() => {
        // Keep fallback products on failure.
      });
  }, []);

  return (
    <section className="px-6 py-16 bg-[#0F0F12]">
      <div className="max-w-6xl mx-auto">
        <div className="flex items-end justify-between mb-8">
          <h2 className="text-3xl font-bold text-white">
            Car <span className="text-[#00FFCC]">Products</span>
          </h2>
          <Link
            href="/products"
            className="text-sm font-medium text-[#00FFCC] hover:underline"
          >
            View all products →
          </Link>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
          {products.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </div>
    </section>
  );
}
