"use client";

import { motion } from "framer-motion";

export interface Car {
  id: string | number;
  image: string;
  name: string;
  year: number;
  mileage: number;
  price: number;
  transmission: string;
  fuelType: string;
}

interface CarCardProps {
  car: Car;
}

export default function CarCard({ car }: CarCardProps) {
  return (
    <motion.div
      className="rounded-2xl bg-[#18181C] overflow-hidden cursor-pointer"
      whileHover={{
        y: -8,
        scale: 1.03,
        boxShadow: "0 0 32px 4px rgba(0, 255, 204, 0.35)",
      }}
      transition={{ type: "spring", stiffness: 300, damping: 20 }}
    >
      <div className="relative h-48 w-full bg-black/40">
        <img
          src={car.image}
          alt={car.name}
          className="h-full w-full object-cover"
        />
      </div>

      <div className="p-5">
        <h3 className="text-lg font-semibold text-white mb-3">{car.name}</h3>

        <div className="grid grid-cols-2 gap-y-2 text-sm text-gray-300">
          <div>
            <span className="text-gray-500">Year</span>
            <p className="text-white">{car.year}</p>
          </div>
          <div>
            <span className="text-gray-500">Mileage</span>
            <p className="text-white">{car.mileage.toLocaleString()} mi</p>
          </div>
          <div>
            <span className="text-gray-500">Transmission</span>
            <p className="text-white">{car.transmission}</p>
          </div>
          <div>
            <span className="text-gray-500">Fuel Type</span>
            <p className="text-white">{car.fuelType}</p>
          </div>
        </div>

        <div className="mt-4 pt-4 border-t border-white/10">
          <span className="text-xl font-bold text-[#00FFCC]">
            £{car.price.toLocaleString()}
          </span>
        </div>
      </div>
    </motion.div>
  );
}
