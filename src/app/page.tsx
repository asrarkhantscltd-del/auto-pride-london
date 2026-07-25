import ShowroomGrid from "@/components/ShowroomGrid";
import { Car } from "@/components/CarCard";
import InquiryForm from "@/components/InquiryForm";

const cars: Car[] = [
  {
    id: 1,
    image: "https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800",
    name: "BMW 3 Series",
    year: 2021,
    mileage: 18500,
    price: 22995,
    transmission: "Automatic",
    fuelType: "Petrol",
  },
  {
    id: 2,
    image: "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800",
    name: "Audi A4",
    year: 2020,
    mileage: 24300,
    price: 20495,
    transmission: "Automatic",
    fuelType: "Diesel",
  },
  {
    id: 3,
    image: "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800",
    name: "Mercedes C-Class",
    year: 2022,
    mileage: 9800,
    price: 27995,
    transmission: "Automatic",
    fuelType: "Hybrid",
  },
];

export default function Home() {
  return (
    <main className="min-h-screen bg-[#0F0F12]">
      <div className="px-6 pt-16 pb-8 text-center">
        <h1 className="text-4xl font-bold text-white">
          Auto Pride <span className="text-[#00FFCC]">London</span>
        </h1>
        <p className="mt-2 text-gray-400">
          Premium refurbished cars, hand-picked for you.
        </p>
      </div>
      <ShowroomGrid cars={cars} />
      <InquiryForm />
    </main>
  );
}
