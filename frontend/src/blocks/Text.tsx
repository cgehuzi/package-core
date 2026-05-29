export type TextProps = {
  body?: string;
};

export function Text({ body }: TextProps) {
  if (!body) return null;

  return (
    <section className="mx-auto max-w-2xl px-6 py-12">
      <p className="text-lg leading-relaxed text-zinc-700">{body}</p>
    </section>
  );
}
