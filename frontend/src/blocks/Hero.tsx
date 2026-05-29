export type HeroProps = {
  heading?: string;
  subheading?: string;
};

export function Hero({ heading, subheading }: HeroProps) {
  return (
    <section className="bg-gradient-to-b from-zinc-900 to-zinc-800 px-6 py-24 text-center text-white">
      {heading && (
        <h1 className="text-4xl font-bold tracking-tight sm:text-6xl">{heading}</h1>
      )}
      {subheading && (
        <p className="mx-auto mt-4 max-w-2xl text-lg text-zinc-300">{subheading}</p>
      )}
    </section>
  );
}
