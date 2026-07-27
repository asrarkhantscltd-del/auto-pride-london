"use client";

import { useEffect, useState } from "react";
import Link from "next/link";

interface SocialLink {
  platform: string;
  url: string;
}

interface SiteInfo {
  business_name: string;
  tagline: string;
  phone: string;
  email: string;
  address: string;
}

const defaultSite: SiteInfo = {
  business_name: "Auto Pride London",
  tagline: "Premium Refurbished Cars & Accessories",
  phone: "",
  email: "",
  address: "",
};

const platformLabels: Record<string, string> = {
  instagram: "IG",
  facebook: "FB",
  tiktok: "TT",
  youtube: "YT",
  twitter: "X",
  whatsapp: "WA",
  snapchat: "SC",
  linkedin: "IN",
};

export default function Footer() {
  const [site, setSite] = useState<SiteInfo>(defaultSite);
  const [socialLinks, setSocialLinks] = useState<SocialLink[]>([]);

  useEffect(() => {
    fetch("/backend/api/settings.php")
      .then((res) => {
        if (!res.ok) throw new Error("Failed to load settings");
        return res.json();
      })
      .then((data: { site?: Partial<SiteInfo>; social_links?: SocialLink[] }) => {
        if (data.site) {
          setSite({ ...defaultSite, ...data.site });
        }
        if (Array.isArray(data.social_links)) {
          setSocialLinks(data.social_links);
        }
      })
      .catch(() => {
        // Keep defaults on failure.
      });
  }, []);

  return (
    <footer className="bg-[#0F0F12] border-t border-white/10">
      <div className="max-w-6xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-3 gap-10">
        <div>
          <h3 className="text-white font-semibold text-lg">{site.business_name}</h3>
          <p className="mt-2 text-sm text-gray-500">{site.tagline}</p>

          {socialLinks.length > 0 && (
            <div className="mt-5 flex gap-2">
              {socialLinks.map((link) => (
                <a
                  key={link.platform}
                  href={link.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-9 h-9 rounded-full bg-[#18181C] border border-white/10 flex items-center justify-center text-xs font-semibold text-gray-300 hover:border-[#00FFCC]/40 hover:text-[#00FFCC] transition-colors"
                  aria-label={link.platform}
                >
                  {platformLabels[link.platform] ?? link.platform.slice(0, 2).toUpperCase()}
                </a>
              ))}
            </div>
          )}
        </div>

        <div>
          <h4 className="text-sm font-semibold text-white uppercase tracking-wide">
            Quick Links
          </h4>
          <div className="mt-3 flex flex-col gap-2 text-sm text-gray-500">
            <Link href="/" className="hover:text-[#00FFCC] transition-colors">
              Home
            </Link>
            <Link href="/cars" className="hover:text-[#00FFCC] transition-colors">
              Cars
            </Link>
            <Link href="/products" className="hover:text-[#00FFCC] transition-colors">
              Car Products
            </Link>
          </div>
        </div>

        <div>
          <h4 className="text-sm font-semibold text-white uppercase tracking-wide">
            Contact
          </h4>
          <div className="mt-3 flex flex-col gap-2 text-sm text-gray-500">
            {site.phone && <span>{site.phone}</span>}
            {site.email && <span>{site.email}</span>}
            {site.address && <span>{site.address}</span>}
          </div>
        </div>
      </div>

      <div className="border-t border-white/10 py-5 text-center text-xs text-gray-600">
        © {new Date().getFullYear()} {site.business_name}. All rights reserved.
      </div>
    </footer>
  );
}
