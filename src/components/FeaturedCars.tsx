"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import CarCard, { Car } from "./CarCard";
import { cars as fallbackCars } from "@/data/cars";

export default function FeaturedCars() {
  const [cars, setCars] = useState<Car[]>(fallbackCars.slice(0, 3));

  useEffect(() => {
    fetch("/backend/api/cars.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load cars");
        return res.json();
      })
      .then((data: Car[]) => setCars(data.slice(0, 3)))
      .catch(() => {
        // Keep fallback cars on failure.
      });
  }, []);

  return (
    <section className="px-6 py-16 bg-[#0F0F12]">
      <div className="max-w-6xl mx-auto">
        <div className="flex items-end justify-between mb-8">
          <h2 className="text-3xl font-bold text-white">
            Featured <span className="text-[#00FFCC]">Cars</span>
          </h2>
          <Link
            href="/cars"
            className="text-sm font-medium text-[#00FFCC] hover:underline"
          >
            View all cars →
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {cars.map((car) => (
            <CarCard key={car.id} car={car} />
          ))}
        </div>
      </div>
    </section>
  );
}
