import ProductsGrid from "@/components/ProductsGrid";
import { products } from "@/data/products";

export default function ProductsPage() {
  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <div className="px-6 pt-16 pb-8 text-center">
        <h1 className="text-4xl font-bold text-white">
          Car <span className="text-[#00FFCC]">Products</span>
        </h1>
        <p className="mt-2 text-gray-400">
          Finish the look — mats, seat covers, mirrors and more.
        </p>
      </div>
      <ProductsGrid products={products} />
    </main>
  );
}
