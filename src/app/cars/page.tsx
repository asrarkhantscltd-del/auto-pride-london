import ShowroomGrid from "@/components/ShowroomGrid";
import { cars } from "@/data/cars";

export default function CarsPage() {
  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <div className="px-6 pt-16 pb-8 text-center">
        <h1 className="text-4xl font-bold text-white">
          Our <span className="text-[#00FFCC]">Cars</span>
        </h1>
        <p className="mt-2 text-gray-400">
          Premium refurbished cars, hand-picked for you.
        </p>
      </div>
      <ShowroomGrid cars={cars} />
    </main>
  );
}
