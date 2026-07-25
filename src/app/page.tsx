import Link from "next/link";
import InquiryForm from "@/components/InquiryForm";

export default function Home() {
  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <div className="px-6 pt-24 pb-16 text-center">
        <h1 className="text-5xl font-bold text-white">
          Auto Pride <span className="text-[#00FFCC]">London</span>
        </h1>
        <p className="mt-4 text-lg text-gray-400 max-w-xl mx-auto">
          Premium refurbished cars and accessories, hand-picked for you.
        </p>

        <div className="mt-10 flex items-center justify-center gap-4">
          <Link
            href="/cars"
            className="px-6 py-3 rounded-xl bg-[#00FFCC] text-black font-semibold hover:scale-105 transition-transform"
          >
            Browse Cars
          </Link>
          <Link
            href="/products"
            className="px-6 py-3 rounded-xl bg-[#18181C] border border-white/10 text-white font-semibold hover:border-[#00FFCC]/40 transition-colors"
          >
            Shop Car Products
          </Link>
        </div>
      </div>

      <InquiryForm />
    </main>
  );
}
