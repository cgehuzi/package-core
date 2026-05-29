import { afterEach, describe, expect, it, vi } from "vitest";
import { fetchHealth } from "../src/api-client";

describe("fetchHealth", () => {
  afterEach(() => {
    vi.restoreAllMocks();
    delete process.env.INTERNAL_API_URL;
  });

  it("обращается к {baseUrl}/core/health и возвращает данные при 200", async () => {
    const payload = { package: "cgehuzi/core-backend", version: "0.0.1", status: "ok" };
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      json: async () => payload,
    });
    vi.stubGlobal("fetch", fetchMock);

    const res = await fetchHealth({ baseUrl: "http://api-internal/api" });

    expect(fetchMock).toHaveBeenCalledWith(
      "http://api-internal/api/core/health",
      expect.objectContaining({ method: "GET", cache: "no-store" }),
    );
    expect(res).toEqual({ ok: true, status: 200, data: payload });
  });

  it("берёт baseUrl из env INTERNAL_API_URL, если опция не задана", async () => {
    process.env.INTERNAL_API_URL = "http://api-internal/api";
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      json: async () => ({ status: "ok" }),
    });
    vi.stubGlobal("fetch", fetchMock);

    await fetchHealth();

    expect(fetchMock).toHaveBeenCalledWith(
      "http://api-internal/api/core/health",
      expect.anything(),
    );
  });

  it("кидает ошибку, если baseUrl негде взять", async () => {
    await expect(fetchHealth()).rejects.toThrow(/INTERNAL_API_URL/);
  });

  it("возвращает ok:false при не-2xx", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn().mockResolvedValue({
        ok: false,
        status: 503,
        json: async () => ({ error: "down" }),
      }),
    );

    const res = await fetchHealth({ baseUrl: "http://x/api" });
    expect(res.ok).toBe(false);
    expect(res.status).toBe(503);
  });
});
