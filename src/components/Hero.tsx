"use client";

import { useEffect, useState } from "react";
import Link from "next/link";

interface HeroData {
  title: string;
  subtitle: string;
  background_image: string;
  background_video: string;
}

const defaultHero: HeroData = {
  title: "Auto Pride London",
  subtitle: "Premium refurbished cars and accessories, hand-picked for you.",
  background_image: "",
  background_video: "",
};

export default function Hero() {
  const [hero, setHero] = useState<HeroData>(defaultHero);

  useEffect(() => {
    fetch("/backend/api/settings.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load settings");
        return res.json();
      })
      .then((data: { hero?: Partial<HeroData> }) => {
        if (data.hero) {
          setHero({
            title: data.hero.title || defaultHero.title,
            subtitle: data.hero.subtitle || defaultHero.subtitle,
            background_image: data.hero.background_image || "",
            background_video: data.hero.background_video || "",
          });
        }
      })
      .catch(() => {
        // Keep default hero on failure.
      });
  }, []);

  const [heading, ...rest] = hero.title.split(" ");
  const accent = rest.join(" ");
  const hasMedia = !!hero.background_video || !!hero.background_image;

  return (
    <div className="relative px-6 pt-24 pb-16 text-center overflow-hidden">
      {hero.background_video && (
        <video
          key={hero.background_video}
          autoPlay
          muted
          loop
          playsInline
          className="absolute inset-0 h-full w-full object-cover"
        >
          <source src={hero.background_video} />
        </video>
      )}
      {!hero.background_video && hero.background_image && (
        <img
          src={hero.background_image}
          alt=""
          className="absolute inset-0 h-full w-full object-cover"
        />
      )}
      {hasMedia && (
        <div className="absolute inset-0 bg-[#0F0F12]/70" />
      )}

      <div className="relative z-10">
        <h1 className="text-5xl font-bold text-white">
          {heading} {accent && <span className="text-[#00FFCC]">{accent}</span>}
        </h1>
        <p className="mt-4 text-lg text-gray-400 max-w-xl mx-auto">
          {hero.subtitle}
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
    </div>
  );
}
