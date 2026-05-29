import { afterEach, describe, expect, it, vi } from "vitest";
import { fetchRender } from "../src/api-client";
import { resolveRoute } from "../src/route";

describe("resolveRoute", () => {
  it("корень без слага → дефолтная локаль и /", () => {
    expect(resolveRoute(undefined)).toEqual({ locale: "ru", path: "/" });
    expect(resolveRoute([])).toEqual({ locale: "ru", path: "/" });
  });

  it("обычный путь без префикса локали", () => {
    expect(resolveRoute(["about"])).toEqual({ locale: "ru", path: "/about" });
    expect(resolveRoute(["a", "b"])).toEqual({ locale: "ru", path: "/a/b" });
  });

  it("префикс известной локали отделяется от пути", () => {
    expect(resolveRoute(["en", "about"])).toEqual({ locale: "en", path: "/about" });
    expect(resolveRoute(["en"])).toEqual({ locale: "en", path: "/" });
  });
});

describe("fetchRender", () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("строит URL /core/render с path и locale и возвращает контракт", async () => {
    const result = {
      status: 200,
      redirect: null,
      locale: "ru",
      route: { type: "page", id: 1 },
      seo: { title: "Home" },
      blocks: [{ type: "hero", data: { heading: "Hi" } }],
    };
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      json: async () => result,
    });
    vi.stubGlobal("fetch", fetchMock);

    const res = await fetchRender("/", "ru", { baseUrl: "http://api-internal/api" });

    expect(fetchMock).toHaveBeenCalledWith(
      "http://api-internal/api/core/render?path=%2F&locale=ru",
      expect.anything(),
    );
    expect(res).toEqual(result);
  });

  it("бросает при транспортной ошибке (не-2xx)", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn().mockResolvedValue({ ok: false, status: 502, json: async () => ({}) }),
    );

    await expect(
      fetchRender("/", "ru", { baseUrl: "http://x/api" }),
    ).rejects.toThrow(/HTTP 502/);
  });
});
