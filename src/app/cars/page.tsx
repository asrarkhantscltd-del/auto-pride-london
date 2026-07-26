"use client";

import { useEffect, useState } from "react";
import ShowroomGrid from "@/components/ShowroomGrid";
import { Car } from "@/components/CarCard";
import { cars as fallbackCars } from "@/data/cars";

export default function CarsPage() {
  const [cars, setCars] = useState<Car[]>(fallbackCars);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetch("/backend/api/cars.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load cars");
        return res.json();
      })
      .then((data: Car[]) => {
        setCars(data);
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
          Our <span className="text-[#00FFCC]">Cars</span>
        </h1>
        <p className="mt-2 text-gray-400">
          Premium refurbished cars, hand-picked for you.
        </p>
        {loading && (
          <p className="mt-4 text-sm text-gray-600">Loading latest listings...</p>
        )}
        {error && (
          <p className="mt-4 text-sm text-yellow-500">
            Couldn&apos;t reach the latest listings — showing recent cars instead.
          </p>
        )}
      </div>
      <ShowroomGrid cars={cars} />
    </main>
  );
}
