"use client";

import { Suspense, useEffect, useState } from "react";
import { useSearchParams } from "next/navigation";
import Link from "next/link";
import CarGallery from "@/components/CarGallery";
import { Car } from "@/components/CarCard";

interface CarWithGallery extends Car {
  gallery: string[];
}

function CarDetailContent() {
  const searchParams = useSearchParams();
  const id = searchParams.get("id");

  const [car, setCar] = useState<CarWithGallery | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    if (!id) {
      setLoading(false);
      setError(true);
      return;
    }

    fetch(`/backend/api/car.php?id=${encodeURIComponent(id)}`)
      .then((res) => {
        if (!res.ok) throw new Error("Not found");
        return res.json();
      })
      .then((data: CarWithGallery) => {
        setCar(data);
        setError(false);
      })
      .catch(() => setError(true))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) {
    return (
      <main className="min-h-screen bg-[#0F0F12] flex items-center justify-center">
        <p className="text-gray-500">Loading car details...</p>
      </main>
    );
  }

  if (error || !car) {
    return (
      <main className="min-h-screen bg-[#0F0F12] flex flex-col items-center justify-center gap-4">
        <p className="text-gray-400">Couldn&apos;t find that car.</p>
        <Link href="/cars/" className="text-[#00FFCC] underline">
          Back to all cars
        </Link>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-[#0F0F12] px-6 py-12">
      <div className="max-w-5xl mx-auto">
        <Link href="/cars/" className="text-gray-400 hover:text-white text-sm">
          ← Back to all cars
        </Link>

        <div className="mt-6 flex flex-wrap items-baseline justify-between gap-4">
          <h1 className="text-3xl sm:text-4xl font-bold text-white">
            {car.name}
          </h1>
          <span className="text-2xl font-bold text-[#00FFCC]">
            £{car.price.toLocaleString()}
          </span>
        </div>

        {car.status === "sold" && (
          <span className="inline-block mt-2 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
            SOLD
          </span>
        )}

        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 mb-10 text-sm">
          <div>
            <span className="text-gray-500 block">Year</span>
            <span className="text-white">{car.year}</span>
          </div>
          <div>
            <span className="text-gray-500 block">Mileage</span>
            <span className="text-white">{car.mileage.toLocaleString()} mi</span>
          </div>
          <div>
            <span className="text-gray-500 block">Transmission</span>
            <span className="text-white">{car.transmission}</span>
          </div>
          <div>
            <span className="text-gray-500 block">Fuel Type</span>
            <span className="text-white">{car.fuelType}</span>
          </div>
        </div>

        <h2 className="text-lg font-semibold text-white mb-4">Photos</h2>
        <CarGallery images={car.gallery} alt={car.name} />
      </div>
    </main>
  );
}

export default function CarDetailPage() {
  return (
    <Suspense
      fallback={
        <main className="min-h-screen bg-[#0F0F12] flex items-center justify-center">
          <p className="text-gray-500">Loading...</p>
        </main>
      }
    >
      <CarDetailContent />
    </Suspense>
  );
}
