import type { ComponentType } from "react";
import type { Block } from "../types";
import { Hero } from "./Hero";
import { Text } from "./Text";

// Реестр блоков: тип из бэкенда → React-компонент. Новый блок — одна строка здесь
// + компонент. Так фронт развязан от контент-модели.
export const blockRegistry: Record<string, ComponentType<Record<string, unknown>>> = {
  hero: Hero as ComponentType<Record<string, unknown>>,
  text: Text as ComponentType<Record<string, unknown>>,
};

export function BlockRenderer({ blocks }: { blocks: Block[] }) {
  return (
    <>
      {blocks.map((block, index) => {
        const Component = blockRegistry[block.type];

        // Неизвестный тип не роняет страницу — просто пропускаем.
        if (!Component) {
          if (process.env.NODE_ENV !== "production") {
            console.warn(`[@cgehuzi/core-frontend] неизвестный блок: "${block.type}"`);
          }
          return null;
        }

        return <Component key={index} {...block.data} />;
      })}
    </>
  );
}
