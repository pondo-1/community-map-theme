export async function fetchSVG(url) {
  const response = await fetch(url);
  if (!response.ok) throw new Error("SVG could not be loaded.");
  return await response.text();
}   