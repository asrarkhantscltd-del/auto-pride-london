"use client";

import { motion } from "framer-motion";
import CarCard, { Car } from "./CarCard";

interface ShowroomGridProps {
  cars: Car[];
}

const containerVariants = {
  hidden: {},
  visible: {
    transition: {
      staggerChildren: 0.12,
    },
  },
};

const cardVariants = {
  hidden: { opacity: 0, y: 40 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.5, ease: "easeOut" },
  },
};

export default function ShowroomGrid({ cars }: ShowroomGridProps) {
  return (
    <motion.div
      className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6 bg-[#0F0F12]"
      variants={containerVariants}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, amount: 0.2 }}
    >
      {cars.map((car) => (
        <motion.div key={car.id} variants={cardVariants}>
          <CarCard car={car} />
        </motion.div>
      ))}
    </motion.div>
  );
}
