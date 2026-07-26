"use client";

import { useEffect, useState } from "react";
import ProductsGrid from "@/components/ProductsGrid";
import { Product } from "@/components/ProductCard";
import { products as fallbackProducts } from "@/data/products";

export default function ProductsPage() {
  const [products, setProducts] = useState<Product[]>(fallbackProducts);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetch("/backend/api/products.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load products");
        return res.json();
      })
      .then((data: Product[]) => {
        setProducts(data);
        setError(false);
      })
      .catch(() => {
        setError(true);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <div className="px-6 pt-16 pb-8 text-center">
        <h1 className="text-4xl font-bold text-white">
          Car <span className="text-[#00FFCC]">Products</span>
        </h1>
        <p className="mt-2 text-gray-400">
          Finish the look — mats, seat covers, mirrors and more.
        </p>
        {loading && (
          <p className="mt-4 text-sm text-gray-600">Loading latest products...</p>
        )}
        {error && (
          <p className="mt-4 text-sm text-yellow-500">
            Couldn&apos;t reach the latest products — showing recent items instead.
          </p>
        )}
      </div>
      <ProductsGrid products={products} />
    </main>
  );
}
