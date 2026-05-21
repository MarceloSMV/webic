:-dynamic tiene/1.
lista([]):-enfermedad(E),write(E).
lista([H|T]):-assert(tiene(H)),lista(T).
test(X) :-limpiar,lista(X).% write( 'Hola mundo cruel ' ),write(X).

%:-dynamic tiene/1.
%enfermedad('A'):-tiene(s1),tiene(s2).
%enfermedad('B'):-tiene(s1),tiene(s3).
%enfermedad('IS'):-tiene(s1),tiene(s3).
%enfermedad('HE'):-tiene(s1),tiene(s4),tiene(s10),tiene(s11).
%
enfermedad('IS'):-tiene(s1),tiene(s3).
enfermedad('HE'):-tiene(s1),tiene(s4),tiene(s10),tiene(s11).
enfermedad('IS'):-(tiene(s8),tiene(s1));(tiene(s8),tiene(s4));(tiene(s8),tiene(s5));(tiene(s8),tiene(s6));(tiene(s8),tiene(s7));(tiene(s8),tiene(s9));(tiene(s8),tiene(s10));(tiene(s8),tiene(s11)).
enfermedad('IS'):-(tiene(s5),tiene(s1));(tiene(s5),tiene(s2));(tiene(s5),tiene(s3));(tiene(s5),tiene(s4));(tiene(s5),tiene(s6));(tiene(s5),tiene(s7));(tiene(s5),tiene(s9));(tiene(s5),tiene(s10));(tiene(s5),tiene(s11)).
enfermedad('IS'):-(tiene(s4),tiene(s1));(tiene(s4),tiene(s2));(tiene(s4),tiene(s3));(tiene(s4),tiene(s6));(tiene(s4),tiene(s7));(tiene(s4),tiene(s9));(tiene(s4),tiene(s10));(tiene(s4),tiene(s11)).
enfermedad('HE'):-(tiene(s10),tiene(s11));(tiene(s10),tiene(s2));(tiene(s10),tiene(s3));(tiene(s10),tiene(s6));(tiene(s10),tiene(s7));(tiene(s10),tiene(s9));(tiene(s10),tiene(s11)).
enfermedad('HE'):-(tiene(s11),tiene(s1));(tiene(s11),tiene(s2));(tiene(s11),tiene(s3));(tiene(s11),tiene(s6));(tiene(s11),tiene(s7));(tiene(s11),tiene(s9));(tiene(s11),tiene(s10)).
enfermedad(_):-fail.
limpiar:-retract(tiene(_)),fail.
limpiar.
