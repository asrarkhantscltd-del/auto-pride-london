import Hero from "@/components/Hero";
import FeaturedCars from "@/components/FeaturedCars";
import FeaturedProducts from "@/components/FeaturedProducts";
import InquiryForm from "@/components/InquiryForm";

export default function Home() {
  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <Hero />
      <FeaturedCars />
      <FeaturedProducts />
      <InquiryForm />
    </main>
  );
}
